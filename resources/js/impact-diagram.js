/**
 * Impact diagram — Alpine component.
 *
 * Ported from the validated interactive prototype
 * (.tmp/prototypes-diagramme-de-relations-métier.tsx) to Alpine.js, without
 * any graph library: absolute positions, SVG edges, Pointer Events.
 *
 * The graph payload is injected inline by the Blade view:
 *   x-data="impactDiagram(JSON.parse('...'))"
 * with { rootId, nodes: [{ id, type, label, url, attributes }],
 *        edges: [{ from, to, rel, label, hidden }] }.
 */

const TYPES = {
    asset: { name: 'Business Asset', plural: 'Business Assets', color: '#2563eb', border: 'border-blue-300', dot: 'bg-blue-500', text: 'text-blue-900', bg: 'bg-blue-50' },
    rule: { name: 'Business Rule', plural: 'Business Rules', color: '#7c3aed', border: 'border-violet-300', dot: 'bg-violet-500', text: 'text-violet-900', bg: 'bg-violet-50' },
    source: { name: 'Data Source', plural: 'Data Sources', color: '#059669', border: 'border-emerald-300', dot: 'bg-emerald-500', text: 'text-emerald-900', bg: 'bg-emerald-50' },
    dq: { name: 'Data Quality Check', plural: 'Data Quality Checks', color: '#d97706', border: 'border-amber-300', dot: 'bg-amber-500', text: 'text-amber-900', bg: 'bg-amber-50' },
};

const RELS = {
    conform: { key: 'conform', color: '#7c3aed', marker: 'm-conform', label: 'must conform to' },
    contained: { key: 'contained', color: '#059669', marker: 'm-contained', label: 'is contained' },
    checked: { key: 'checked', color: '#d97706', marker: 'm-checked', label: 'is checked by', hiddenByDefault: true },
    applies: { key: 'applies', color: '#f43f5e', marker: 'm-applies', label: 'applies to', hiddenByDefault: true },
};

// Geometry constants (same as the prototype).
const CW = 190, H = 52, VGAP = 18, TOP = 62, GAP = 84, PAD = 14, X0 = 16, GW = 170;

window.impactDiagram = (graph) => ({
    // ---- static data -----------------------------------------------------
    types: TYPES,
    relsList: Object.values(RELS),

    // ---- reactive state ---------------------------------------------------
    view: 'blocks',
    sel: null,
    dqDeployed: false,
    order: { source: [], asset: [], rule: [], dq: [] },
    graphPos: {},
    drag: null,

    // populated in init()
    nodes: [],
    edges: [],
    nodeById: {},

    init() {
        this.nodes = graph.nodes;
        this.edges = graph.edges;
        this.nodeById = Object.fromEntries(graph.nodes.map((node) => [node.id, node]));

        // Initial per-type order follows the payload order (backend sorted).
        graph.nodes.forEach((node) => this.order[node.type].push(node.id));

        // Default free-graph positions: one column per type, stacked by order.
        ['source', 'asset', 'rule', 'dq'].forEach((type, col) => {
            this.order[type].forEach((id, idx) => {
                this.graphPos[id] = { x: 16 + col * (GW + GAP), y: 20 + idx * (H + 34) };
            });
        });
    },

    // ---- derived data -----------------------------------------------------
    get colTypes() {
        const order = ['source', 'asset', 'rule'];
        if (this.dqDeployed) {
            order.push('dq');
        }

        return order;
    },

    get visibleIds() {
        return this.nodes.filter((node) => node.type !== 'dq' || this.dqDeployed).map((node) => node.id);
    },

    get visibleEdges() {
        return this.edges.filter((edge) => !edge.hidden || this.dqDeployed);
    },

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

    hiddenCount(id) {
        return this.edges.filter((edge) => edge.hidden && (edge.from === id || edge.to === id)).length;
    },

    get blocksGeo() {
        const xs = {};
        let x = X0;
        this.colTypes.forEach((type) => {
            xs[type] = x;
            x += CW + GAP;
        });

        const blocks = {};
        this.colTypes.forEach((type) => {
            const count = this.order[type].length;
            const bottom = TOP + count * (H + VGAP) - VGAP;
            blocks[type] = { x: xs[type] - PAD, y: 10, w: CW + 2 * PAD, h: bottom - 10 + 16, cx: xs[type] + CW / 2, bottom: bottom + 6 };
        });

        const maxBottom = Math.max(...Object.values(blocks).map((b) => b.y + b.h));
        const laneY = maxBottom + 36;
        const width = X0 + this.colTypes.length * CW + (this.colTypes.length - 1) * GAP + X0;
        const height = this.dqDeployed ? laneY + 34 : maxBottom + 24;

        return { xs, blocks, width, height, laneY };
    },

    /**
     * Aggregated block-to-block arcs (blocks view): one arc per
     * (from-type, to-type, relation) group, labelled "rel · count".
     */
    get blockArcs() {
        const geo = this.blocksGeo;
        const types = this.colTypes;
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
            const b1 = geo.blocks[group.ft];
            const b2 = geo.blocks[group.tt];
            const i1 = types.indexOf(group.ft);
            const i2 = types.indexOf(group.tt);
            const hi = Boolean(this.sel) && group.edges.some((edge) => edge.from === this.sel || edge.to === this.sel);
            const stroke = hi ? rel.color : this.sel ? '#cbd5e1' : rel.color;
            const opacity = hi ? 1 : this.sel ? 0.85 : 0.5;
            const strokeWidth = hi ? 3 : 2;
            const marker = hi || !this.sel ? rel.marker : 'm-dim';
            const labelText = `${rel.label} · ${group.edges.length}`;
            const labelColor = hi ? rel.color : '#94a3b8';

            let d, lx, ly;
            if (Math.abs(i1 - i2) === 1) {
                const leftward = i1 > i2;
                const sx = leftward ? b1.x : b1.x + b1.w;
                const sy = b1.y + b1.h / 2;
                const tx = leftward ? b2.x + b2.w : b2.x;
                const ty = b2.y + b2.h / 2;
                lx = (sx + tx) / 2;
                ly = (sy + ty) / 2 - 7;
                d = `M ${sx} ${sy} C ${lx} ${sy}, ${lx} ${ty}, ${tx} ${ty}`;
            } else {
                // Non-adjacent blocks: orthogonal routing below the blocks.
                const fx = b1.cx;
                const fy = b1.y + b1.h;
                const txx = b2.cx;
                const tyy = b2.y + b2.h;
                d = `M ${fx} ${fy} V ${geo.laneY} H ${txx} V ${tyy}`;
                lx = (fx + txx) / 2;
                ly = geo.laneY - 8;
            }

            return { key: `${group.ft}|${group.tt}|${group.rel}`, d, stroke, opacity, strokeWidth, marker, labelText, labelColor, lx, ly };
        });
    },

    /**
     * Individual labelled edges (free graph view).
     */
    get graphArcs() {
        const layout = {};
        this.visibleIds.forEach((id) => {
            const pos = this.graphPos[id] || { x: 0, y: 0 };
            layout[id] = { x: pos.x, y: pos.y, w: GW, h: H };
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

    get canvasSize() {
        if (this.view === 'blocks') {
            const geo = this.blocksGeo;

            return { width: geo.width, height: geo.height };
        }

        let maxX = 300;
        let maxY = 200;
        this.visibleIds.forEach((id) => {
            const pos = this.graphPos[id];
            maxX = Math.max(maxX, pos.x);
            maxY = Math.max(maxY, pos.y);
        });

        return { width: maxX + GW + 24, height: maxY + H + 24 };
    },

    get currentArcs() {
        return this.view === 'blocks' ? this.blockArcs : this.graphArcs;
    },

    geomOf(id) {
        const type = this.nodeById[id].type;
        if (this.view === 'blocks') {
            const slot = this.order[type].indexOf(id);

            return { x: this.blocksGeo.xs[type], y: TOP + slot * (H + VGAP), w: CW, h: H };
        }

        const pos = this.graphPos[id] || { x: 0, y: 0 };

        return { x: pos.x, y: pos.y, w: GW, h: H };
    },

    // ---- detail panel -----------------------------------------------------
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
            }));
    },

    get hiddenCarrierLabels() {
        if (this.dqDeployed) {
            return [];
        }

        const ids = new Set(
            this.edges.filter((edge) => edge.hidden).flatMap((edge) => [edge.from, edge.to]),
        );

        return [...ids]
            .filter((id) => this.nodeById[id].type !== 'dq')
            .map((id) => this.nodeById[id].label);
    },

    // ---- interactions -----------------------------------------------------
    select(id) {
        this.sel = this.sel === id ? null : id;
    },

    clearSelection() {
        this.sel = null;
    },

    startDrag(event, id) {
        if (event.pointerType === 'mouse' && event.button !== 0) {
            return;
        }

        const rect = this.$refs.area.getBoundingClientRect();
        const g = this.geomOf(id);
        const lx = event.clientX - rect.left;
        const ly = event.clientY - rect.top;
        this.drag = { id, offX: lx - g.x, offY: ly - g.y, startX: lx, startY: ly, moved: false };
        event.currentTarget.setPointerCapture(event.pointerId);
    },

    moveDrag(event) {
        const d = this.drag;
        if (!d) {
            return;
        }

        const rect = this.$refs.area.getBoundingClientRect();
        const lx = event.clientX - rect.left;
        const ly = event.clientY - rect.top;

        if (!d.moved && (Math.abs(lx - d.startX) > 4 || Math.abs(ly - d.startY) > 4)) {
            d.moved = true;
        }
        if (!d.moved) {
            return;
        }

        if (this.view === 'blocks') {
            const type = this.nodeById[d.id].type;
            const count = this.order[type].length;
            let idx = Math.round((ly - d.offY - TOP) / (H + VGAP));
            idx = Math.max(0, Math.min(count - 1, idx));

            const arr = [...this.order[type]];
            const current = arr.indexOf(d.id);
            if (current !== idx) {
                arr.splice(current, 1);
                arr.splice(idx, 0, d.id);
                this.order = { ...this.order, [type]: arr };
            }
        } else {
            const { width, height } = this.canvasSize;
            const x = Math.max(4, Math.min(width - GW - 4, lx - d.offX));
            const y = Math.max(4, Math.min(height - H - 4, ly - d.offY));
            this.graphPos = { ...this.graphPos, [d.id]: { x, y } };
        }
    },

    endDrag() {
        const d = this.drag;
        this.drag = null;
        if (d && !d.moved) {
            this.select(d.id);
        }
    },

    toggleDq() {
        this.dqDeployed = !this.dqDeployed;
    },
});
