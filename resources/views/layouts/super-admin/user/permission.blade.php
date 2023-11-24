<table class="table">
    <thead>
        <tr>
            <th>Permission</th>
            <th>Assign</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($permissions as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>
                    <div class="form-check form-switch d-inline-block col">
                        <input class="form-check-input" id="permission{{ $item->id }}" name="permissions[]"
                            type="checkbox" value="{{ $item->id }}" data-permission-id="{{ $item->id }}"
                            {{ in_array($item->id, $user->permissions->pluck('id')->toArray()) ? 'checked' : '' }}>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


<script>
    $(document).ready(function() {
        $('.form-check-input').on('change', function() {
            var permissionId = $(this).data('permission-id');
            var userId = @json($user->id);

            $.post({
                url: $(this).prop('checked') ? "{{route('superadmin.user.grand-permission')}}" : "{{route('superadmin.user.revoke-permission')}}",
                data: {
                    userId: userId,
                    permissionId: permissionId
                },
                success: response => console.log(response),
                error: error => console.error(error)
            });
        });
    });
</script>
