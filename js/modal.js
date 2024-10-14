function modalBase() {
    return $('#modal_base');
}

async function modalContent(content_path) {
    let content = await $.ajax({
        url: `/modules/${content_path}`,
        method: 'GET',
        headers: {
            "Autorization": $.session.get('token')
        }
    });

    return content;
}

function openModal(content_path) {
    let base = modalBase();

    modalContent(content_path)
        .then((content) => {
            $(base).find('.modal-content').html(content);
        });    

    $(base).modal("show");
}