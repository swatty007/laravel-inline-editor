@if (Gate::allows('laravel-inline-editor'))
    <inline-manager inline-template>
        <div id="inline-manager">
            <ul>
                <li @click="applyChanges" title="Apply changes">
                    Apply changes
                </li>
            </ul>
        </div>
    </inline-manager>
@endif