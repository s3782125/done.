function openModal(modalId)
{
    const modal = document.getElementById(modalId);
    modal.style.display = "block";
}

function closeModal(modalId)
{
    const modal = document.getElementById(modalId);
    modal.style.display = "none";
}

// Closes the modal when clicking outside the box
// window.onclick = function (event)
// {
//     const modal = document.getElementById("myModal");
//     if (event.target === modal)
//     {
//         modal.style.display = "none";
//     }
// }