<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        @php
            $data = $getRecord(); // Assuming $getRecord() returns the data array
        @endphp
        
        @if(isset($data->data['old_relationships']))
            @foreach ($data->data['old_relationships'] as $relationshipKey => $attributes)
                <p class="my-4">{{ ucfirst(str_replace('_', ' ', $relationshipKey)) }}</p>

                <div class="fi-in-key-value w-full rounded-lg bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                    <table class="w-full table-auto divide-y divide-gray-200 dark:divide-white/5">
            
                        <tbody class="divide-y divide-gray-200 font-mono text-base dark:divide-white/5 sm:text-sm sm:leading-6">
                            
                            @if (!empty($attributes))
                                @foreach ($attributes as $key => $value)
                                    <tr class="divide-x divide-gray-200 dark:divide-white/5 rtl:divide-x-reverse">
                                        <td class="w-1/2 px-3 py-1.5">
                                            {{ ucfirst($key) }}
                                        </td>
                
                                        <td class="w-1/2 px-3 py-1.5">
                                            {{ $value }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif
    </div>
</x-dynamic-component>


