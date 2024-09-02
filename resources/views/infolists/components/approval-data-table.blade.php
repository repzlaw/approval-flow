<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        @php
            $data = $getRecord();
            $oldRecord = $data->approvable_type::find($data->approvable_id);
        @endphp

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
                    @if(isset($data->data['new']))
                    @foreach ($data->data['new'] as $key => $value)
                        <tr class="divide-x divide-gray-200 dark:divide-white/5 rtl:divide-x-reverse">
                            <td class="w-1/3 px-3 py-1.5">
                                {{ ucfirst($key) }}
                            </td>
    
                            <td class="w-1/3 px-3 py-1.5"
                            style="word-wrap: break-word; overflow-wrap: break-word; 
                            text-overflow: ellipsis; white-space: normal; word-break: break-all;">
                                {{ $value }}
                            </td>
                            @if($data->operation == 'Edit')
                            <td class="w-1/3 px-3 py-1.5"
                            style="word-wrap: break-word; overflow-wrap: break-word; 
                            text-overflow: ellipsis; white-space: normal; word-break: break-all;">
                                {{ $oldRecord->{$key} ?? '' }}
                            </td>
                            @endif
                        </tr>
                    @endforeach

                        {{-- @if (!empty($attributes))
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
                        @endif --}}
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</x-dynamic-component>
