function checkImageSize(target)
{
    let message = document.getElementById("imageErrorMessage");
    let submit = document.getElementById("submitNewItem");
    let file = target.files[0];

    if (file.type.indexOf("image") === -1)
    {
        message.innerText = "File is not supported"
        submit.disabled = true;
    }
    else if (file.size > 1000000)
    {
        message.innerText = "Image is too big (max 1MB)"
        submit.disabled = true;
    }
    else
    {
        message.innerText = ""
        submit.disabled = false;
    }
}

function toggleNewList()
{
    let form = document.getElementById("newListForm")

    if (form.style.display === "block")
        form.style.display = "none";
    else
        form.style.display = "block";
}