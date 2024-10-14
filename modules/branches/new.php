<div class="modal-header">
    <h5 class="modal-title">Nova Branch</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form id="new_branch_form">
        <div class="mb-3">
            <label for="branch_name" class="form-label">Nome da Branch</label>
            <input type="text" class="form-control" id="branch_name" name="branch_name" required>
        </div>
        <div class="mb-3">
            <label for="branch_description" class="form-label">Descrição(opcional)</label>
            <textarea class="form-control" id="branch_description" name="branch_description" rows="3"></textarea>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button type="button" class="btn btn-sm btn-success" onclick="newBranch()">Confirmar</button>
</div>
<script>

    function newBranch() {
        requestNewBranch()
            .then((response) => {
                if (response.TYPE == 'SUCCESS') {                    
                    loadModule('branches');
                    $('#modal_base').modal('hide');
                }
            });
    }

    async function requestNewBranch() {
        let response = await $.ajax({
            url: '/api/branches/create',
            method: 'POST',
            headers: {
                "Authorization": $.session.get('token')
            },
            data: $('#new_branch_form').serialize()
        });

        return response;
    }

</script>