<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        @php
            $data = $getRecord();

            $modelClass = $data->approvable_type; 
            $model = new $modelClass(); 

            if (isset($model) && property_exists($model, 'approvable_relationships')) {
                $reflector = new ReflectionClass($model);
                $property = $reflector->getProperty('approvable_relationships');
                $property->setAccessible(true);
                $approvable_relationships = $property->getValue($model);
            }
        @endphp
        
        @if(isset($model) && property_exists($model, 'approvable_relationships'))
            <div class="fi-in-key-value w-full rounded-lg bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                <table class="w-full table-auto divide-y divide-gray-200 dark:divide-white/5">
        
                    <tbody class="divide-y divide-gray-200 font-mono text-base dark:divide-white/5 sm:text-sm sm:leading-6">
                        <tr class="divide-x divide-gray-200 dark:divide-white/5 rtl:divide-x-reverse">
                            <td class="w-1/3 px-3 py-1.5 font-bold">
                                Key
                            </td>
                            <td class="w-1/3 px-3 py-1.5 font-bold">
                                New Value
                            </td>
                            @if($data->operation == 'Edit')
                            <td class="w-1/3 px-3 py-1.5 font-bold">
                                Old Value
                            </td>
                            @endif
                        </tr>
                    @foreach ($data->data['new_relationships'] as $relationshipKey => $attributes)

                        @if (!empty($attributes))
                            @foreach ($attributes as $key => $value)
                                <tr class="divide-x divide-gray-200 dark:divide-white/5 rtl:divide-x-reverse">
                                    <td class="w-1/3 px-3 py-1.5">
                                        {{ ucfirst(str_replace('_id', ' ', $relationshipKey)) }} {{ ucfirst($key) }}
                                    </td>
            
                                    @php
                                        if (isset($model) && property_exists($model, 'approvable_relationships')) {
                                            $newRelationshipModel = $approvable_relationships[$relationshipKey]::find($data->data['new'][$relationshipKey]);
                                        }
                                    @endphp

                                    <td class="w-1/3 px-3 py-1.5"
                                    style="word-wrap: break-word; overflow-wrap: break-word; 
                                    text-overflow: ellipsis; white-space: normal; word-break: break-all;">
                                        {{$newRelationshipModel->{$key} ?? ''}}
                                    </td>

                                    @if(isset($data->data['old_relationships']) && $data->operation == 'Edit')

                                    @php
                                        if (isset($model) && property_exists($model, 'approvable_relationships')) {
                                            $oldRelationshipModel = $approvable_relationships[$relationshipKey]::find($data->data['old'][$relationshipKey]);
                                        }
                                    @endphp

                                    <td class="w-1/3 px-3 py-1.5"
                                        style="word-wrap: break-word; overflow-wrap: break-word; 
                                        text-overflow: ellipsis; white-space: normal; word-break: break-all;">
                                        {{$oldRelationshipModel->{$key} ?? ''}}
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-dynamic-component>


