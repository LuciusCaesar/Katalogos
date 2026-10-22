<x-layouts::app :title="__('Impact Diagram')">
    @vite('resources/js/impact-diagram.js')
    <div x-data="impactDiagram({!! $graph !!})" class="space-y-4" @keyup.escape.window="clearSelection()">
        {{-- Header --}}
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Impact Diagram') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Starting from') }}: <span class="font-semibold">{{ $rootLabel }}</span>
                </p>
            </div>
            <div class="inline-flex rounded-xl border border-slate-200 bg-white shadow-sm p-1 gap-1">
                <button
                    type="button"
                    @click="view = 'blocks'"
                    :class="view === 'blocks' ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors"
                >
                    {{ __('▦ Blocks (columns)') }}
                </button>
                <button
                    type="button"
                    @click="view = 'graph'"
                    :class="view === 'graph' ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors"
                >
                    {{ __('⬡ Free graph') }}
                </button>
            </div>
        </div>

        <p class="text-sm text-slate-500" x-text="view === 'blocks'
            ? '{{ __('One block per type, stacked and reorderable elements. Links connect blocks with a counter; highlighting reveals which elements are related.') }}'
            : '{{ __('Each element is a free, draggable node. Individual links with always-visible relationship labels.') }}'">
        </p>

        {{-- Legend --}}
        <div class="flex items-center gap-4 text-xs text-slate-500 flex-wrap">
            <template x-for="rel in relsList" :key="rel.key">
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-6 h-0.5 rounded" :style="`background: ${rel.color}`"></span>
                    <span x-text="rel.label"></span>
                    <span x-show="rel.hiddenByDefault" class="text-amber-600 font-semibold">({{ __('hidden by default ⚡') }})</span>
                </span>
            </template>
            <button x-show="sel" type="button" @click="clearSelection()" class="text-xs text-slate-500 hover:text-slate-800 underline">
                ✕ {{ __('deselect') }}
            </button>
        </div>

        {{-- Diagram card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex">
                {{-- Diagram area --}}
                <div class="flex-1 min-w-0 overflow-x-auto">
                    <div
                        x-ref="area"
                        class="relative"
                        :style="`width: ${canvasSize.width}px; height: ${canvasSize.height}px;`"
                        @click="if ($event.target === $event.currentTarget) clearSelection()"
                    >
                        {{-- Block backgrounds + headers (blocks view) --}}
                        <template x-if="view === 'blocks'">
                            <div>
                                <template x-for="type in colTypes" :key="'bg-' + type">
                                    <div
                                        class="absolute rounded-2xl border border-slate-200 bg-slate-50/70 pointer-events-none"
                                        :style="`left: ${blocksGeo.blocks[type].x}px; top: ${blocksGeo.blocks[type].y}px; width: ${blocksGeo.blocks[type].w}px; height: ${blocksGeo.blocks[type].h}px;`"
                                    ></div>
                                </template>
                                <template x-for="type in colTypes" :key="'hd-' + type">
                                    <div
                                        class="absolute z-20 text-[11px] font-bold uppercase tracking-wider flex items-center gap-2 pointer-events-none"
                                        :style="`left: ${blocksGeo.blocks[type].x + 18}px; top: 22px; color: ${types[type].color};`"
                                    >
                                        <span x-text="types[type].plural + ' (' + order[type].length + ')'"></span>
                                        <span x-show="type === 'dq'" class="text-slate-400 normal-case font-normal">— {{ __('click ⚡ or ✕ to retract') }}</span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Edges (SVG layer under the cards) --}}
                        {{-- NOTE: <template x-for> cannot be used inside <svg> (SVG-namespace --}}
                        {{-- templates have no .content), so markers are static and arcs are --}}
                        {{-- rendered via the x-html markup string built by the component. --}}
                        <svg class="absolute z-[5]" :width="canvasSize.width" :height="canvasSize.height" style="pointer-events: none;">
                            <defs>
                                <marker id="m-conform" viewBox="0 0 10 10" refX="8.5" refY="5" markerWidth="6.5" markerHeight="6.5" orient="auto-start-reverse">
                                    <path d="M0,0 L10,5 L0,10 z" fill="#7c3aed"></path>
                                </marker>
                                <marker id="m-contained" viewBox="0 0 10 10" refX="8.5" refY="5" markerWidth="6.5" markerHeight="6.5" orient="auto-start-reverse">
                                    <path d="M0,0 L10,5 L0,10 z" fill="#059669"></path>
                                </marker>
                                <marker id="m-checked" viewBox="0 0 10 10" refX="8.5" refY="5" markerWidth="6.5" markerHeight="6.5" orient="auto-start-reverse">
                                    <path d="M0,0 L10,5 L0,10 z" fill="#d97706"></path>
                                </marker>
                                <marker id="m-applies" viewBox="0 0 10 10" refX="8.5" refY="5" markerWidth="6.5" markerHeight="6.5" orient="auto-start-reverse">
                                    <path d="M0,0 L10,5 L0,10 z" fill="#f43f5e"></path>
                                </marker>
                                <marker id="m-dim" viewBox="0 0 10 10" refX="8.5" refY="5" markerWidth="6.5" markerHeight="6.5" orient="auto-start-reverse">
                                    <path d="M0,0 L10,5 L0,10 z" fill="#cbd5e1"></path>
                                </marker>
                            </defs>
                            <g x-html="arcsMarkup"></g>
                        </svg>

                        {{-- Cards --}}
                        <template x-for="id in visibleIds" :key="id">
                            <button
                                type="button"
                                :aria-pressed="sel === id"
                                @pointerdown="startDrag($event, id)"
                                @pointermove="moveDrag($event)"
                                @pointerup="endDrag()"
                                @pointercancel="endDrag()"
                                class="absolute z-10 rounded-lg border-2 shadow-sm select-none cursor-grab active:cursor-grabbing text-left focus:outline-none"
                                :class="sel === id
                                    ? `${types[nodeById[id].type].border} ${types[nodeById[id].type].bg}`
                                    : (isLit(id)
                                        ? `${types[nodeById[id].type].border} bg-white hover:shadow-md transition-all duration-200`
                                        : 'border-slate-200 bg-white opacity-40 hover:shadow-md transition-all duration-200')"
                                :style="`left: ${geomOf(id).x}px; top: ${geomOf(id).y}px; width: ${geomOf(id).w}px; height: ${geomOf(id).h}px; box-shadow: ${sel === id ? `0 0 0 4px ${types[nodeById[id].type].color}26` : ''};`"
                            >
                                <div class="flex items-center h-full px-2.5 gap-2">
                                    <span class="w-2 h-2 rounded-full shrink-0" :class="types[nodeById[id].type].dot"></span>
                                    <div class="leading-tight text-left min-w-0">
                                        <div class="font-semibold text-sm truncate" :class="types[nodeById[id].type].text" x-text="nodeById[id].label"></div>
                                        <div x-show="view === 'graph'" class="text-[8px] uppercase tracking-wider text-slate-400" x-text="types[nodeById[id].type].name"></div>
                                    </div>
                                    <span
                                        x-show="hiddenCount(id) > 0"
                                        @pointerdown.stop
                                        @click.stop="toggleDq()"
                                        :title="dqDeployed ? '{{ __('Retract deployed relations') }}' : hiddenCount(id) + '{{ __(' hidden relation(s) — click to deploy') }}'"
                                        class="ml-auto text-[10px] font-bold rounded-full px-1.5 py-0.5 border cursor-pointer select-none transition-colors"
                                        :class="dqDeployed
                                            ? 'bg-amber-200 border-amber-400 text-amber-900'
                                            : (sel === id
                                                ? 'bg-amber-100 border-amber-400 text-amber-800 animate-pulse'
                                                : 'bg-amber-50 border-amber-300 text-amber-700 hover:bg-amber-100')"
                                        x-text="dqDeployed ? '–' : '⚡' + hiddenCount(id)"
                                    ></span>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Detail panel --}}
                <div class="w-72 shrink-0 border-l border-slate-200 p-4 overflow-y-auto bg-white max-h-[70vh]">
                    <template x-if="!selectedNode">
                        <div class="text-sm text-slate-400">
                            <p class="font-semibold text-slate-500 mb-1">{{ __('Detail panel') }}</p>
                            <p>{{ __('Click an element of the diagram to see the detail of its relationships.') }}</p>
                            <div x-show="hiddenCarrierLabels.length > 0" class="mt-4 rounded-lg border border-amber-300 bg-amber-50 p-2.5">
                                <div class="text-xs font-semibold text-amber-800">⚡ {{ __('Hidden relations') }}</div>
                                <p class="text-[11px] text-amber-700 mt-0.5">
                                    <span x-text="hiddenCarrierLabels.join(', ')"></span>
                                    {{ __('have relations to non-deployed Data Quality elements.') }}
                                </p>
                                <button type="button" @click="dqDeployed = true" class="mt-1.5 text-[11px] font-semibold px-2 py-1 rounded-md bg-amber-500 text-white hover:bg-amber-600">
                                    {{ __('Deploy ⚡') }}
                                </button>
                            </div>
                        </div>
                    </template>
                    <template x-if="selectedNode">
                        <div>
                            {{-- Header --}}
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" :class="types[selectedNode.type].dot"></span>
                                <span class="font-bold text-slate-800" x-text="selectedNode.label"></span>
                                <span class="text-[10px] uppercase tracking-wider text-slate-400" x-text="types[selectedNode.type].name"></span>
                            </div>
                            <a :href="selectedNode.url" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline mb-3">
                                {{ __('Open page') }} ↗
                            </a>

                            {{-- Characteristics --}}
                            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('Characteristics') }}</div>
                            <div class="space-y-1 mb-3">
                                <template x-for="[key, value] in Object.entries(selectedNode.attributes)" :key="key">
                                    <div class="flex gap-2 text-xs">
                                        <span class="font-medium text-slate-500 shrink-0" x-text="key + ':'"></span>
                                        <span class="text-slate-700 break-words min-w-0" x-text="value || '—'"></span>
                                    </div>
                                </template>
                            </div>

                            {{-- Relations --}}
                            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                                <span x-text="'{{ __('Relations') }}' + ' (' + selectedEdges.length + ')'"></span>
                            </div>
                            <div class="space-y-2">
                                <template x-for="edge in selectedEdges" :key="edge.from + '|' + edge.to">
                                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
                                        <div class="flex items-center gap-1.5 text-xs flex-wrap">
                                            <span class="text-slate-400 font-bold" x-text="edge.isOut ? '→' : '←'"></span>
                                            <span class="italic text-slate-500" x-text="edge.label"></span>
                                            <span x-show="edge.hidden && !dqDeployed" class="text-[10px] font-bold text-amber-600">⚡ {{ __('hidden') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                            <button
                                                type="button"
                                                @click="select(edge.otherId)"
                                                class="text-xs font-semibold px-2 py-0.5 rounded-md border hover:shadow-sm"
                                                :class="`${types[nodeById[edge.otherId].type].border} ${types[nodeById[edge.otherId].type].bg} ${types[nodeById[edge.otherId].type].text}`"
                                                x-text="nodeById[edge.otherId].label"
                                            ></button>
                                            <button
                                                x-show="edge.hidden && !dqDeployed"
                                                type="button"
                                                @click="dqDeployed = true"
                                                class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-amber-500 text-white hover:bg-amber-600"
                                            >
                                                {{ __('Deploy ⚡') }}
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="clearSelection()" class="mt-3 text-xs text-slate-400 hover:text-slate-600 underline">
                                {{ __('deselect') }}
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <p class="text-xs text-slate-400">
            {{ __('Tips: click an element to highlight it with its direct neighbors; drag it to reorder (blocks view) or move it freely (graph view). The ⚡ badge signals hidden relations — click it to deploy the Data Quality block.') }}
        </p>
    </div>
</x-layouts::app>
