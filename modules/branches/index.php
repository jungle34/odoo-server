<div class="container">
    <div class="row">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Branches</li>
                </ol>
            </nav>
        </div>
        <div class="col">
            <button class="btn btn-success btn-sm float-end" id="new_branch" type="button">
                <i class="bi bi-plus"></i> Nova Branch
            </button>
        </div>
    </div>
    <div class="row mt-3" id="branches_area">

    </div>
</div>


<script>

    $(document).ready(function() {
        setBranchEvents();
    });

    function setBranchEvents() {
        setNewBranchEvent();
    }

    function setNewBranchEvent() {
        $('#new_branch').click(function() {
            openModal('branches/new.php');
        });
    }

</script>