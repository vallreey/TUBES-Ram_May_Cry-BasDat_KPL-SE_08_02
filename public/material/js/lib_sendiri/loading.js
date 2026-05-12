// LIBRARY LOCAL: Loading GIF

window.addEventListener("load", function () {
    const loader = document.getElementById("page-loader");

    setTimeout(() => {
        loader.classList.add("hide");
    }, 500);
});
