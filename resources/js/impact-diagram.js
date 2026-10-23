/**
 * Impact diagram — Alpine component.
 *
 * Ported from the validated interactive prototype
 * (.tmp/prototypes-diagramme-de-relations-métier.tsx) to Alpine.js, without
 * any graph library: absolute positions, SVG edges, Pointer Events.
 *
 * Layout — dynamically balanced force relaxation: springs pull related
 * bodies (blocks in the blocks view, elements in the free graph view)
 * toward a short target distance, size-aware repulsion keeps the bodies
 * apart, and a light gravity keeps the graph centered on the entry point.
 * Freshly revealed bodies start AT their revealer, so they always appear
 * next to the element they are extended from. Previously placed bodies
 * keep their position (continuity); manual drags are pinned.
 *
 * Navigation — the canvas is bigger than the viewport: drag the background
 * or scroll with the mouse wheel / trackpad to pan in all directions.
 *
 * Progressive disclosure: initially only the root and its direct relations
 * (depth <= 1) are visible. Every visible node carries a ⚡ badge counting
 * its not-yet-visible relations; clicking it extends the graph from that
 * node (click again to retract).
 *
 * The graph payload is injected inline by the Blade view:
 *   x-data="impactDiagram(JSON.parse('...'))"
 * with { rootId, nodes: [{ id, type, label, url, depth, attributes }],
 *        edges: [{ from, to, rel, label }] }.
 */

const TYPES = {
    asset: { name: 'Business Asset', plural: 'Business Assets', color: '#2563eb', border: 'border-blue-300', dot: 'bg-blue-500', text: 'text-blue-900', bg: 'bg-blue-50' },
    rule: { name: 'Business Rule', plural: 'Business Rules', color: '#7c3aed', border: 'border-violet-300', dot: 'bg-violet-500', text: 'text-violet-900', bg: 'bg-violet-50' },
    source: { name: 'Data Source', plural: 'Data Sources', color: '#059669', border: 'border-emerald-300', dot: 'bg-emerald-500', text: 'text-emerald-900', bg: 'bg-emerald-50' },
    dq: { name: 'Data Quality Check', plural: 'Data Quality Checks', color: '#d97706', border: 'border-amber-300', dot: 'bg-amber-500', text: 'text-amber-900', bg: 'bg-amber-50' },
    initiative: { name: 'Data Initiative', plural: 'Data Initiatives', color: '#c026d3', border: 'border-fuchsia-300', dot: 'bg-fuchsia-500', text: 'text-fuchsia-900', bg: 'bg-fuchsia-50' },
    objective: { name: 'Business Objective', plural: 'Business Objectives', color: '#0284c7', border: 'border-sky-300', dot: 'bg-sky-500', text: 'text-sky-900', bg: 'bg-sky-50' },
    domain: { name: 'Domain', plural: 'Domains', color: '#0891b2', border: 'border-cyan-300', dot: 'bg-cyan-500', text: 'text-cyan-900', bg: 'bg-cyan-50' },
    issue: { name: 'Data Issue', plural: 'Data Issues', color: '#dc2626', border: 'border-red-300', dot: 'bg-red-500', text: 'text-red-900', bg: 'bg-red-50' },
    rootcause: { name: 'Root Cause', plural: 'Root Causes', color: '#ea580c', border: 'border-orange-300', dot: 'bg-orange-500', text: 'text-orange-900', bg: 'bg-orange-50' },
    solution: { name: 'Solution', plural: 'Solutions', color: '#0d9488', border: 'border-teal-300', dot: 'bg-teal-500', text: 'text-teal-900', bg: 'bg-teal-50' },
    process: { name: 'Business Process', plural: 'Business Processes', color: '#65a30d', border: 'border-lime-300', dot: 'bg-lime-500', text: 'text-lime-900', bg: 'bg-lime-50' },
    event: { name: 'Business Event', plural: 'Business Events', color: '#db2777', border: 'border-pink-300', dot: 'bg-pink-500', text: 'text-pink-900', bg: 'bg-pink-50' },
};

const RELS = {
    conform: { key: 'conform', color: '#7c3aed', marker: 'm-conform', label: 'must conform to' },
    contained: { key: 'contained', color: '#059669', marker: 'm-contained', label: 'is contained' },
    checked: { key: 'checked', color: '#d97706', marker: 'm-checked', label: 'is checked by' },
    applies: { key: 'applies', color: '#f43f5e', marker: 'm-applies', label: 'applies to' },
    'used-by': { key: 'used-by', color: '#65a30d', marker: 'm-used-by', label: 'uses' },
    affects: { key: 'affects', color: '#dc2626', marker: 'm-affects', label: 'affects' },
    'in-domain': { key: 'in-domain', color: '#0891b2', marker: 'm-in-domain', label: 'belongs to' },
    'in-initiative': { key: 'in-initiative', color: '#c026d3', marker: 'm-in-initiative', label: 'is part of' },
    'contributes-to': { key: 'contributes-to', color: '#0284c7', marker: 'm-contributes-to', label: 'contributes to' },
    violates: { key: 'violates', color: '#ea580c', marker: 'm-violates', label: 'violates' },
    'caused-by': { key: 'caused-by', color: '#be123c', marker: 'm-caused-by', label: 'is caused by' },
    'solved-by': { key: 'solved-by', color: '#0d9488', marker: 'm-solved-by', label: 'is solved by' },
    'subject-of': { key: 'subject-of', color: '#db2777', marker: 'm-subject-of', label: 'is subject of' },
    'occurs-in': { key: 'occurs-in', color: '#f472b6', marker: 'm-occurs-in', label: 'occurs in' },
};

/**
 * Stable ordering of the types (angular order inside the tree sectors).
 */
const BLOCK_ORDER = ['process', 'event', 'objective', 'initiative', 'domain', 'asset', 'source', 'dq', 'rule', 'issue', 'rootcause', 'solution'];

// Geometry constants (same card metrics as the prototype).
const CW = 190, H = 52, VGAP = 18, PAD = 14, GW = 170;
// Block interior: header height above the first card.
const HEAD = 62;
// Force-balanced layout: target distance between related bodies.
const NODE_REST = 220;
const BLOCK_REST = 440;

window.impactDiagram = (graph) => ({
    // ---- static data -----------------------------------------------------
    types: TYPES,
    relsList: Object.values(RELS),
    blockOrder: BLOCK_ORDER,

    // ---- reactive state ---------------------------------------------------
    view: 'blocks',
    sel: null,
    expanded: [],
    order: {},
    blockPos: {},
    defaultBlockPos: {},
    drag: null,
    blockDrag: null,
    panDrag: null,
    pan: { x: 0, y: 0 },
    zoom: 1,
    minZoom: 0.25,
    maxZoom: 3,
    suppressClick: false,

    rootId: graph.rootId,
    rootType: '',
    nodes: graph.nodes,
    edges: graph.edges,
    nodeById: {},
    nodeIndex: new Map(),
    defaultBlockPos: {},
    defaultNodePos: {},
    manualNodePos: {},

    init() {
        this.nodeById = Object.fromEntries(graph.nodes.map((node) => [node.id, node]));
        this.nodeIndex = new Map(graph.nodes.map((node, index) => [node.id, index]));
        this.rootType = this.nodeById[this.rootId].type;

        // Initial order inside each block follows the payload order
        // (sorted by depth, then type, then id by the backend).
        BLOCK_ORDER.forEach((type) => {
            this.order[type] = graph.nodes.filter((node) => node.type === type).map((node) => node.id);
        });

        // Snowflake layout driven by the current visibility / extension state.
        this.rebuildLayout();
    },

    /**
     * Parent of a visible element in the layout tree — derived from the
     * current state so retraction is handled automatically:
     * - first-degree elements hang under the root;
     * - a never-expanded element hangs under its most recently expanded
     *   neighbour (the element the user extended FROM);
     * - an EXPANDED element hangs under the most recent expansion that is
     *   strictly EARLIER than its own. Parent pointers therefore always
     *   descend in expansion order, which makes cycles impossible — without
     *   this guard, two mutually-related expanded elements could parent each
     *   other and both fall back to the center position (overlapping boxes).
     */
    revealParent(id) {
        const node = this.nodeById[id];
        if (node.depth <= 1) {
            return this.rootId;
        }

        const ownIndex = this.expanded.indexOf(id);
        for (let i = this.expanded.length - 1; i >= 0; i -= 1) {
            if (ownIndex !== -1 && i >= ownIndex) {
                continue; // an expanded element hangs under an earlier expansion only
            }
            const expandedId = this.expanded[i];
            if (this.adjacent(expandedId, id)) {
                return expandedId;
            }
        }

        return this.rootId;
    },

    /**
     * Whether two elements are directly related.
     */
    adjacent(a, b) {
        return this.edges.some((edge) => (edge.from === a && edge.to === b) || (edge.to === a && edge.from === b));
    },

    /**
     * Recompute the layout from the CURRENT visible graph, balancing it
     * dynamically (force relaxation): springs pull related bodies toward a
     * short target distance, bodies repel each other (size-aware, so blocks
     * do not overlap), and a light gravity keeps the graph centered on the
     * entry point. Previously placed bodies keep their position as the
     * starting point of the relaxation (continuity); freshly revealed
     * bodies start AT their revealer so they appear right next to it.
     * Manual drags (blockPos / manualNodePos) are pinned.
     */
    rebuildLayout() {
        // ---- element level (free graph view) ----
        const nodeLinks = [];
        const seenPair = new Set();
        this.visibleEdges.forEach((edge) => {
            const key = edge.from < edge.to ? `${edge.from}|${edge.to}` : `${edge.to}|${edge.from}`;
            if (!seenPair.has(key)) {
                seenPair.add(key);
                nodeLinks.push({ a: edge.from, b: edge.to });
            }
        });

        const seedNodes = { [this.rootId]: { x: 0, y: 0 } };
        const freshNodesByParent = new Map();
        this.visibleNodeIds.forEach((id) => {
            if (id === this.rootId) {
                return;
            }
            const previous = this.defaultNodePos[id];
            if (previous) {
                seedNodes[id] = { x: previous.x, y: previous.y };

                return;
            }
            const parent = this.revealParent(id);
            if (!freshNodesByParent.has(parent)) {
                freshNodesByParent.set(parent, []);
            }
            freshNodesByParent.get(parent).push(id);
        });
        freshNodesByParent.forEach((ids, parent) => {
            const anchor = seedNodes[parent] || { x: 0, y: 0 };
            ids.forEach((id, index) => {
                const angle = (2 * Math.PI * (index + 0.25)) / ids.length;
                seedNodes[id] = { x: anchor.x + Math.cos(angle) * 120, y: anchor.y + Math.sin(angle) * 120 };
            });
        });

        const nodePins = new Set([this.rootId, ...Object.keys(this.manualNodePos)]);
        const nodeIterations = this.visibleNodeIds.length > 150 ? 80 : 200;
        const nodeSize = () => ({ a: GW / 2 + 5, b: H / 2 + 5 });
        this.defaultNodePos = this.relax(seedNodes, nodePins, nodeLinks, NODE_REST, nodeIterations, nodeSize);
        this.resolveOverlaps(this.defaultNodePos, nodePins, nodeSize);

        // ---- block level ----
        // Each visible type hangs under the type of the revealer of its most
        // recently revealed element, so fresh blocks anchor next to the
        // block they were extended from.
        const typeParent = new Map();
        this.visibleNodeIds.forEach((id) => {
            if (id === this.rootId) {
                return;
            }
            const childType = this.nodeById[id].type;
            if (childType === this.rootType) {
                return;
            }
            const parent = this.revealParent(id);
            const parentType = this.nodeById[parent].type;
            if (parentType === childType) {
                return;
            }
            const recency = this.expanded.indexOf(parent);
            const current = typeParent.get(childType);
            if (!current || recency > current.recency) {
                typeParent.set(childType, { recency, parentType });
            }
        });

        // The node-level parents are acyclic by construction, but the TYPE
        // level can still loop (e.g. a rule revealed from an issue and
        // another issue revealed from a rule). Any type whose parent chain
        // does not reach the root type is re-attached to the root.
        typeParent.forEach((entry, childType) => {
            let current = childType;
            const seen = new Set();
            while (current !== this.rootType && !seen.has(current)) {
                seen.add(current);
                const next = typeParent.get(current);
                if (!next) {
                    return;
                }
                current = next.parentType;
            }
            if (current !== this.rootType) {
                typeParent.set(childType, { recency: entry.recency, parentType: this.rootType });
            }
        });

        const blockLinks = [];
        const seenTypePair = new Set();
        this.visibleEdges.forEach((edge) => {
            const ft = this.nodeById[edge.from].type;
            const tt = this.nodeById[edge.to].type;
            if (ft === tt) {
                return;
            }
            const key = ft < tt ? `${ft}|${tt}` : `${tt}|${ft}`;
            if (!seenTypePair.has(key)) {
                seenTypePair.add(key);
                blockLinks.push({ a: ft, b: tt });
            }
        });

        // Half-extents of a type block (rectangle body for the layout forces).
        const blockSize = (type) => {
            const count = this.visibleIdsOf(type).length;
            const height = HEAD + count * (H + VGAP) - VGAP + 16;

            return { a: (CW + 2 * PAD) / 2, b: height / 2 };
        };

        const seedBlocks = { [this.rootType]: { x: 0, y: 0 } };
        const freshTypes = [];
        this.visibleTypes.forEach((type) => {
            if (type === this.rootType) {
                return;
            }
            const previous = this.defaultBlockPos[type];
            if (previous) {
                seedBlocks[type] = { x: previous.x, y: previous.y };
            } else {
                freshTypes.push(type);
            }
        });
        freshTypes.forEach((type, index) => {
            const parentType = (typeParent.get(type) || {}).parentType || this.rootType;
            const anchor = seedBlocks[parentType] || { x: 0, y: 0 };
            const angle = (2 * Math.PI * (index + 0.125)) / Math.max(1, freshTypes.length);
            seedBlocks[type] = { x: anchor.x + Math.cos(angle) * 200, y: anchor.y + Math.sin(angle) * 200 };
        });

        const blockPins = new Set([this.rootType, ...Object.keys(this.blockPos)]);
        this.defaultBlockPos = this.relax(seedBlocks, blockPins, blockLinks, BLOCK_REST, 150, blockSize);
        this.resolveOverlaps(this.defaultBlockPos, blockPins, blockSize);
    },

    /**
     * Deterministic positional pass guaranteeing that no two bodies
     * overlap: overlapping pairs are pushed apart along their center axis
     * until their RECTANGLE projections on that axis no longer intersect
     * (pinned bodies never move; the free body takes the full correction).
     * Runs after the force relaxation, which alone may leave small overlaps.
     */
    resolveOverlaps(positions, pinned, bodySize) {
        const keys = Object.keys(positions);
        const pinnedSet = pinned instanceof Set ? pinned : new Set(pinned);

        for (let pass = 0; pass < 30; pass += 1) {
            let moved = false;

            for (let i = 0; i < keys.length; i += 1) {
                for (let j = i + 1; j < keys.length; j += 1) {
                    const a = keys[i];
                    const b = keys[j];
                    let dx = positions[b].x - positions[a].x;
                    let dy = positions[b].y - positions[a].y;
                    let d = Math.hypot(dx, dy);
                    if (d < 1) {
                        dx = 1;
                        dy = 0;
                        d = 1;
                    }
                    const ux = dx / d;
                    const uy = dy / d;
                    // Rectangle projection on the center axis: exact
                    // separation condition along that axis.
                    const sa = bodySize(a);
                    const sb = bodySize(b);
                    const minDist = sa.a * Math.abs(ux) + sa.b * Math.abs(uy)
                        + sb.a * Math.abs(ux) + sb.b * Math.abs(uy) + 20;
                    if (d >= minDist) {
                        continue;
                    }
                    const correction = (minDist - d) / d;
                    const uxCorrection = dx * correction;
                    const uyCorrection = dy * correction;

                    const aPinned = pinnedSet.has(a);
                    const bPinned = pinnedSet.has(b);
                    if (aPinned && bPinned) {
                        continue;
                    }
                    if (aPinned) {
                        positions[b].x += uxCorrection;
                        positions[b].y += uyCorrection;
                    } else if (bPinned) {
                        positions[a].x -= uxCorrection;
                        positions[a].y -= uyCorrection;
                    } else {
                        positions[a].x -= uxCorrection / 2;
                        positions[a].y -= uyCorrection / 2;
                        positions[b].x += uxCorrection / 2;
                        positions[b].y += uyCorrection / 2;
                    }
                    moved = true;
                }
            }

            if (!moved) {
                break;
            }
        }
    },

    /**
     * Deterministic force relaxation producing a compact, balanced layout
     * with short links:
     * - springs along the links pull the bodies toward the rest length;
     * - pairwise repulsion keeps the bodies apart, with a size-aware push
     *   based on rectangle projections when two bodies would overlap;
     * - a light gravity pulls toward the origin so the graph stays centered
     *   on the entry point;
     * - pinned bodies (manual drags, the root) never move.
     */
    relax(seed, pinned, links, restLength, iterations, bodySize) {
        const keys = Object.keys(seed).filter((key) => seed[key]);
        if (keys.length <= 1) {
            const fixed = {};
            keys.forEach((key) => {
                fixed[key] = { x: seed[key].x, y: seed[key].y };
            });

            return fixed;
        }

        const pos = {};
        const vel = {};
        keys.forEach((key) => {
            pos[key] = { x: seed[key].x, y: seed[key].y };
            vel[key] = { x: 0, y: 0 };
        });
        const pinnedSet = pinned instanceof Set ? pinned : new Set(pinned);

        const K_SPRING = 0.05;
        const K_PUSH = 0.25;
        const K_SPREAD = 1600;
        const K_GRAVITY = 0.0025;
        const DAMPING = 0.6;
        const MAX_STEP = 45;
        const MARGIN = 30;

        for (let iteration = 0; iteration < iterations; iteration += 1) {
            const force = {};
            keys.forEach((key) => {
                force[key] = { x: 0, y: 0 };
            });

            // Pairwise repulsion: size-aware collision push plus a mild
            // general spread.
            for (let i = 0; i < keys.length; i += 1) {
                for (let j = i + 1; j < keys.length; j += 1) {
                    const a = keys[i];
                    const b = keys[j];
                    let dx = pos[b].x - pos[a].x;
                    let dy = pos[b].y - pos[a].y;
                    let d = Math.hypot(dx, dy);
                    if (d < 1) {
                        dx = 1;
                        dy = 0;
                        d = 1;
                    }
                    const ux = dx / d;
                    const uy = dy / d;
                    const minDist = (() => {
                        const sa = bodySize(a);
                        const sb = bodySize(b);

                        return sa.a * Math.abs(ux) + sa.b * Math.abs(uy)
                            + sb.a * Math.abs(ux) + sb.b * Math.abs(uy) + MARGIN;
                    })();
                    let push = K_SPREAD / (d * d);
                    if (d < minDist) {
                        push += (minDist - d) * K_PUSH;
                    }
                    force[a].x -= ux * push;
                    force[a].y -= uy * push;
                    force[b].x += ux * push;
                    force[b].y += uy * push;
                }
            }

            // Springs along the links.
            links.forEach(({ a, b }) => {
                if (!pos[a] || !pos[b]) {
                    return;
                }
                const dx = pos[b].x - pos[a].x;
                const dy = pos[b].y - pos[a].y;
                const d = Math.max(1, Math.hypot(dx, dy));
                const f = (d - restLength) * K_SPRING;
                const ux = dx / d;
                const uy = dy / d;
                force[a].x += ux * f;
                force[a].y += uy * f;
                force[b].x -= ux * f;
                force[b].y -= uy * f;
            });

            // Gravity + integration with damping and step clamping.
            keys.forEach((key) => {
                if (pinnedSet.has(key)) {
                    vel[key] = { x: 0, y: 0 };

                    return;
                }
                force[key].x -= pos[key].x * K_GRAVITY;
                force[key].y -= pos[key].y * K_GRAVITY;
                vel[key].x = (vel[key].x + force[key].x) * DAMPING;
                vel[key].y = (vel[key].y + force[key].y) * DAMPING;
                const speed = Math.hypot(vel[key].x, vel[key].y);
                if (speed > MAX_STEP) {
                    vel[key].x = (vel[key].x / speed) * MAX_STEP;
                    vel[key].y = (vel[key].y / speed) * MAX_STEP;
                }
                pos[key].x += vel[key].x;
                pos[key].y += vel[key].y;
            });
        }

        return pos;
    },

    // ---- progressive visibility -------------------------------------------
    isNodeVisible(id) {
        const node = this.nodeById[id];
        if (node.depth <= 1) {
            return true;
        }

        return this.edges.some((edge) => {
            if (edge.from !== id && edge.to !== id) {
                return false;
            }
            const other = edge.from === id ? edge.to : edge.from;

            return this.expanded.includes(other);
        });
    },

    isEdgeVisible(edge) {
        if (!this.isNodeVisible(edge.from) || !this.isNodeVisible(edge.to)) {
            return false;
        }

        if (edge.from === this.rootId || edge.to === this.rootId) {
            return true;
        }

        return this.expanded.includes(edge.from) || this.expanded.includes(edge.to);
    },

    get visibleNodeIds() {
        return this.nodes.filter((node) => this.isNodeVisible(node.id)).map((node) => node.id);
    },

    get visibleEdges() {
        return this.edges.filter((edge) => this.isEdgeVisible(edge));
    },

    /**
     * Count of a node's relations that are not visible yet — the ⚡ badge.
     */
    hiddenCount(id) {
        return this.edges.filter((edge) => (edge.from === id || edge.to === id) && !this.isEdgeVisible(edge)).length;
    },

    get expandableLabels() {
        return this.visibleNodeIds
            .filter((id) => this.hiddenCount(id) > 0)
            .map((id) => ({ id, label: this.nodeById[id].label, count: this.hiddenCount(id) }));
    },

    toggleExpand(id) {
        this.expanded = this.expanded.includes(id)
            ? this.expanded.filter((expandedId) => expandedId !== id)
            : [...this.expanded, id];

        // Newly revealed elements hang under the extended element.
        this.rebuildLayout();
    },

    // ---- selection ----------------------------------------------------------
    get neighbors() {
        const set = new Set();
        if (this.sel) {
            this.visibleEdges.forEach((edge) => {
                if (edge.from === this.sel) {
                    set.add(edge.to);
                }
                if (edge.to === this.sel) {
                    set.add(edge.from);
                }
            });
        }

        return set;
    },

    isLit(id) {
        return !this.sel || id === this.sel || this.neighbors.has(id);
    },

    select(id) {
        this.sel = this.sel === id ? null : id;
    },

    clearSelection() {
        this.sel = null;
    },

    // ---- snowflake layout -----------------------------------------------------
    /**
     * Types with at least one visible element.
     */
    get visibleTypes() {
        return BLOCK_ORDER.filter((type) => this.order[type].some((id) => this.isNodeVisible(id)));
    },

    /**
     * Visible elements of a given type, in display order.
     */
    visibleIdsOf(type) {
        return this.order[type].filter((id) => this.isNodeVisible(id));
    },

    /**
     * Slot of an element among the VISIBLE elements of its block — ensures
     * cards always sit inside their block, whose height only counts the
     * visible elements.
     */
    visibleSlot(type, id) {
        return this.visibleIdsOf(type).indexOf(id);
    },

    /**
     * Center of a type block in snowflake coordinates. Manual drags take
     * precedence; otherwise the block floats at its radial tree position.
     */
    blockCenter(type) {
        return this.blockPos[type] || this.defaultBlockPos[type] || { x: 0, y: 0 };
    },

    /**
     * Rectangle of a type block in snowflake coordinates.
     */
    blockRect(type) {
        const count = this.visibleIdsOf(type).length;
        const w = CW + 2 * PAD;
        const h = HEAD + count * (H + VGAP) - VGAP + 16;
        const center = this.blockCenter(type);

        return { x: center.x - w / 2, y: center.y - h / 2, w, h };
    },

    /**
     * Translation applied to snowflake coordinates so everything fits in
     * the positive quadrant of the canvas.
     */
    get origin() {
        let minX = 0;
        let minY = 0;
        let maxX = 900;
        let maxY = 600;

        if (this.view === 'blocks') {
            this.visibleTypes.forEach((type) => {
                const rect = this.blockRect(type);
                minX = Math.min(minX, rect.x);
                minY = Math.min(minY, rect.y);
                maxX = Math.max(maxX, rect.x + rect.w);
                maxY = Math.max(maxY, rect.y + rect.h);
            });
        } else {
            this.visibleNodeIds.forEach((id) => {
                const pos = this.manualNodePos[id] || this.defaultNodePos[id] || { x: 0, y: 0 };
                minX = Math.min(minX, pos.x - GW / 2);
                minY = Math.min(minY, pos.y - H / 2);
                maxX = Math.max(maxX, pos.x + GW / 2);
                maxY = Math.max(maxY, pos.y + H / 2);
            });
        }

        return { x: -minX + 40, y: -minY + 40, width: maxX - minX + 80, height: maxY - minY + 80 };
    },

    get canvasSize() {
        return { width: this.origin.width, height: this.origin.height };
    },

    /**
     * Rendered rectangle of a type block (snowflake + origin + pan).
     */
    renderBlockRect(type) {
        const rect = this.blockRect(type);

        return { ...rect, x: rect.x + this.origin.x, y: rect.y + this.origin.y };
    },

    /**
     * Rendered geometry of an element card.
     */
    geomOf(id) {
        const type = this.nodeById[id].type;
        if (this.view === 'blocks') {
            const rect = this.renderBlockRect(type);
            const slot = this.visibleSlot(type, id);

            return { x: rect.x + PAD, y: rect.y + HEAD + slot * (H + VGAP), w: CW, h: H };
        }

        const pos = this.manualNodePos[id] || this.defaultNodePos[id] || { x: 0, y: 0 };

        return { x: pos.x + this.origin.x - GW / 2, y: pos.y + this.origin.y - H / 2, w: GW, h: H };
    },

    // ---- edges ---------------------------------------------------------------
    /**
     * Point where the center-to-center direction exits a rectangle border.
     */
    borderExit(rect, dirX, dirY) {
        const cx = rect.x + rect.w / 2;
        const cy = rect.y + rect.h / 2;
        const tx = dirX !== 0 ? (rect.w / 2) / Math.abs(dirX) : Number.POSITIVE_INFINITY;
        const ty = dirY !== 0 ? (rect.h / 2) / Math.abs(dirY) : Number.POSITIVE_INFINITY;
        const t = Math.min(tx, ty);

        return { x: cx + dirX * t, y: cy + dirY * t };
    },

    /**
     * Aggregated block-to-block arcs (blocks view): one arc per
     * (from-type, to-type, relation) group, labelled "rel · count".
     * An arc is highlighted when one of its constituent edges is
     * incident to the selected element (spec F4).
     */
    get blockArcs() {
        const groups = {};

        this.visibleEdges.forEach((edge) => {
            const ft = this.nodeById[edge.from].type;
            const tt = this.nodeById[edge.to].type;
            const key = `${ft}|${tt}|${edge.rel}`;
            if (!groups[key]) {
                groups[key] = { ft, tt, rel: edge.rel, edges: [] };
            }
            groups[key].edges.push(edge);
        });

        return Object.values(groups).map((group) => {
            const rel = RELS[group.rel];
            const b1 = this.renderBlockRect(group.ft);
            const b2 = this.renderBlockRect(group.tt);
            const c1 = { x: b1.x + b1.w / 2, y: b1.y + b1.h / 2 };
            const c2 = { x: b2.x + b2.w / 2, y: b2.y + b2.h / 2 };
            const dx = c2.x - c1.x;
            const dy = c2.y - c1.y;
            const length = Math.max(1, Math.hypot(dx, dy));
            const ux = dx / length;
            const uy = dy / length;

            const s = this.borderExit(b1, ux, uy);
            const t = this.borderExit(b2, -ux, -uy);
            const cs = { x: s.x + ux * 50, y: s.y + uy * 50 };
            const ce = { x: t.x - ux * 50, y: t.y - uy * 50 };
            const d = `M ${s.x} ${s.y} C ${cs.x} ${cs.y}, ${ce.x} ${ce.y}, ${t.x} ${t.y}`;

            const hi = Boolean(this.sel) && group.edges.some((edge) => edge.from === this.sel || edge.to === this.sel);
            const stroke = hi ? rel.color : this.sel ? '#cbd5e1' : rel.color;
            const opacity = hi ? 1 : this.sel ? 0.85 : 0.5;
            const strokeWidth = hi ? 3 : 2;
            const marker = hi || !this.sel ? rel.marker : 'm-dim';

            return {
                key: `${group.ft}|${group.tt}|${group.rel}`,
                d,
                stroke,
                opacity,
                strokeWidth,
                marker,
                labelText: `${rel.label} · ${group.edges.length}`,
                labelColor: hi ? rel.color : '#94a3b8',
                lx: (s.x + t.x) / 2,
                ly: (s.y + t.y) / 2 - 6,
            };
        });
    },

    /**
     * Individual labelled edges (free graph view).
     */
    get graphArcs() {
        const layout = {};
        this.visibleNodeIds.forEach((id) => {
            const g = this.geomOf(id);
            layout[id] = { x: g.x, y: g.y, w: GW, h: H };
        });

        const arcs = [];
        this.visibleEdges.forEach((edge, index) => {
            const s = layout[edge.from];
            const t = layout[edge.to];
            if (!s || !t) {
                return;
            }

            const rel = RELS[edge.rel];
            const hi = Boolean(this.sel) && (edge.from === this.sel || edge.to === this.sel);
            const stroke = hi ? rel.color : this.sel ? '#cbd5e1' : rel.color;
            const opacity = hi ? 1 : this.sel ? 0.9 : 0.45;
            const strokeWidth = hi ? 2.5 : 1.5;
            const marker = hi || !this.sel ? rel.marker : 'm-dim';
            const labelColor = hi ? rel.color : '#94a3b8';

            const leftward = t.x + t.w / 2 < s.x + s.w / 2;
            const sx = leftward ? s.x : s.x + s.w;
            const sy = s.y + s.h / 2;
            const tx = leftward ? t.x + t.w : t.x;
            const ty = t.y + t.h / 2;

            let d, lx, ly;
            if (leftward && sx - tx > 300) {
                // Long "return" link: curve plunging below the nodes.
                d = `M ${sx} ${sy} C ${sx - 100} ${sy + 60}, ${tx + 100} ${ty + 60}, ${tx} ${ty}`;
                lx = (sx + tx) / 2;
                ly = (sy + ty) / 2 + 30;
            } else {
                const mid = (sx + tx) / 2;
                d = `M ${sx} ${sy} C ${mid} ${sy}, ${mid} ${ty}, ${tx} ${ty}`;
                lx = mid;
                ly = (sy + ty) / 2 - 6;
            }

            arcs.push({ key: index, d, stroke, opacity, strokeWidth, marker, labelText: rel.label, labelColor, lx, ly });
        });

        return arcs;
    },

    get currentArcs() {
        return this.view === 'blocks' ? this.blockArcs : this.graphArcs;
    },

    /**
     * SVG markup for the current arcs, injected via x-html on a <g>.
     * (Alpine <template x-for> cannot be used inside <svg>: SVG-namespace
     * templates have no .content, so the arcs are built as a string.)
     */
    get arcsMarkup() {
        return this.currentArcs
            .map((arc) => {
                const label = String(arc.labelText)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;');

                return `<path d="${arc.d}" fill="none" stroke="${arc.stroke}" stroke-opacity="${arc.opacity}" stroke-width="${arc.strokeWidth}" stroke-linecap="round" marker-end="url(#${arc.marker})"></path>`
                    + `<text x="${arc.lx}" y="${arc.ly}" text-anchor="middle" font-size="9.5" font-weight="600" fill="${arc.labelColor}" stroke="#ffffff" stroke-width="4" paint-order="stroke" stroke-linejoin="round">${label}</text>`;
            })
            .join('');
    },

    // ---- detail panel ---------------------------------------------------------
    get selectedNode() {
        return this.sel ? this.nodeById[this.sel] : null;
    },

    get selectedEdges() {
        if (!this.sel) {
            return [];
        }

        return this.edges
            .filter((edge) => edge.from === this.sel || edge.to === this.sel)
            .map((edge) => ({
                ...edge,
                isOut: edge.from === this.sel,
                otherId: edge.from === this.sel ? edge.to : edge.from,
                otherVisible: this.isNodeVisible(edge.from === this.sel ? edge.to : edge.from),
                visible: this.isEdgeVisible(edge),
            }));
    },

    /**
     * Clicking a related node chip selects it when visible, otherwise it
     * extends the graph from the selected node and selects the new node.
     */
    selectRelated(edge) {
        if (!edge.otherVisible) {
            this.expanded = this.expanded.includes(this.sel)
                ? this.expanded
                : [...this.expanded, this.sel];
            this.rebuildLayout();
        }

        this.sel = edge.otherId;
    },

    // ---- pointer interactions ---------------------------------------------------
    pointerPos(event) {
        const rect = this.$refs.area.getBoundingClientRect();

        // Canvas coordinates: the wrapper transform is
        // translate(pan) scale(zoom), so screen = pan + zoom * canvas.
        return {
            x: (event.clientX - rect.left - this.pan.x) / this.zoom,
            y: (event.clientY - rect.top - this.pan.y) / this.zoom,
        };
    },

    /**
     * Wheel handling — standard canvas gestures: plain wheel/trackpad pans,
     * ctrl/cmd+wheel zooms (also matches trackpad pinch gestures on Mac).
     */
    onWheel(event) {
        if (event.ctrlKey || event.metaKey) {
            const factor = Math.max(0.8, Math.min(1.25, Math.exp(-event.deltaY * 0.01)));
            this.zoomAt(event.clientX, event.clientY, factor);

            return;
        }

        this.panBy(event.deltaX, event.deltaY);
    },

    /**
     * Zoom keeping the canvas point under the given screen position fixed
     * (zoom-to-cursor, the standard behavior).
     */
    zoomAt(clientX, clientY, factor) {
        const rect = this.$refs.area.getBoundingClientRect();
        const cx = clientX - rect.left;
        const cy = clientY - rect.top;
        const newZoom = Math.max(this.minZoom, Math.min(this.maxZoom, this.zoom * factor));

        if (newZoom === this.zoom) {
            return;
        }

        // Canvas point under the cursor, kept fixed by adjusting the pan.
        const px = (cx - this.pan.x) / this.zoom;
        const py = (cy - this.pan.y) / this.zoom;
        this.pan = { x: cx - newZoom * px, y: cy - newZoom * py };
        this.zoom = newZoom;
    },

    /**
     * Zoom in/out from the buttons, centered on the viewport.
     */
    zoomBy(factor) {
        const rect = this.$refs.area.getBoundingClientRect();
        this.zoomAt(rect.left + rect.width / 2, rect.top + rect.height / 2, factor);
    },

    /**
     * Reset zoom and pan to the initial view.
     */
    resetView() {
        this.zoom = 1;
        this.pan = { x: 0, y: 0 };
    },

    startDrag(event, id) {
        event.stopPropagation();
        if (event.pointerType === 'mouse' && event.button !== 0) {
            return;
        }

        const g = this.geomOf(id);
        const p = this.pointerPos(event);
        this.drag = { id, offX: p.x - g.x, offY: p.y - g.y, startX: p.x, startY: p.y, moved: false };
        event.currentTarget.setPointerCapture(event.pointerId);
    },

    startBlockDrag(event, type) {
        event.stopPropagation();
        if (event.pointerType === 'mouse' && event.button !== 0) {
            return;
        }

        const center = this.blockCenter(type);
        const p = this.pointerPos(event);
        this.blockDrag = { type, offX: p.x - this.origin.x - center.x, offY: p.y - this.origin.y - center.y };
        event.currentTarget.setPointerCapture(event.pointerId);
    },

    startPan(event) {
        if (event.pointerType === 'mouse' && event.button !== 0) {
            return;
        }

        this.panDrag = { startX: event.clientX, startY: event.clientY, originX: this.pan.x, originY: this.pan.y, moved: false };
        this.$refs.area.setPointerCapture(event.pointerId);
    },

    /**
     * Click on the empty background deselects — unless it closes a pan.
     */
    backgroundClick(event) {
        if (event.target !== event.currentTarget) {
            return;
        }
        if (this.suppressClick) {
            this.suppressClick = false;

            return;
        }
        this.clearSelection();
    },

    panBy(dx, dy) {
        this.pan = { x: this.pan.x - dx, y: this.pan.y - dy };
    },

    moveDrag(event) {
        if (this.panDrag) {
            const dx = event.clientX - this.panDrag.startX;
            const dy = event.clientY - this.panDrag.startY;
            if (!this.panDrag.moved && (Math.abs(dx) > 4 || Math.abs(dy) > 4)) {
                this.panDrag.moved = true;
            }
            this.pan = {
                x: this.panDrag.originX + dx,
                y: this.panDrag.originY + dy,
            };

            return;
        }

        const p = this.pointerPos(event);

        if (this.blockDrag) {
            this.blockPos = {
                ...this.blockPos,
                [this.blockDrag.type]: {
                    x: p.x - this.origin.x - this.blockDrag.offX,
                    y: p.y - this.origin.y - this.blockDrag.offY,
                },
            };

            return;
        }

        const d = this.drag;
        if (!d) {
            return;
        }

        if (!d.moved && (Math.abs(p.x - d.startX) > 4 || Math.abs(p.y - d.startY) > 4)) {
            d.moved = true;
        }
        if (!d.moved) {
            return;
        }

        if (this.view === 'blocks') {
            // Vertical drag reorders the element inside its type block,
            // among the VISIBLE elements (invisible ones keep their relative
            // positions around them).
            const type = this.nodeById[d.id].type;
            const rect = this.renderBlockRect(type);
            const visible = [...this.visibleIdsOf(type)];
            let idx = Math.round((p.y - d.offY - rect.y - HEAD) / (H + VGAP));
            idx = Math.max(0, Math.min(visible.length - 1, idx));

            const currentIdx = visible.indexOf(d.id);
            if (currentIdx !== -1 && currentIdx !== idx) {
                const arr = [...this.order[type]];
                arr.splice(arr.indexOf(d.id), 1);
                const anchor = visible[idx];
                const anchorPos = arr.indexOf(anchor);
                arr.splice(currentIdx < idx ? anchorPos + 1 : anchorPos, 0, d.id);
                this.order = { ...this.order, [type]: arr };
            }
        } else {
            const { width, height } = this.canvasSize;
            const x = Math.max(4, Math.min(width - GW - 4, p.x - d.offX));
            const y = Math.max(4, Math.min(height - H - 4, p.y - d.offY));
            this.manualNodePos = { ...this.manualNodePos, [d.id]: { x: x - this.origin.x + GW / 2, y: y - this.origin.y + H / 2 } };
        }
    },

    endDrag() {
        const d = this.drag;
        if (this.panDrag && this.panDrag.moved) {
            this.suppressClick = true;
        }
        this.drag = null;
        this.blockDrag = null;
        this.panDrag = null;
        if (d && !d.moved) {
            this.select(d.id);
        }
    },
});
