const dropDownOptions = document.querySelectorAll("header .dropdown-item")

if (localStorage.getItem("defLang")) {
    document.querySelector("header #dropdown1-text").textContent = localStorage.getItem("defLang")
    document.querySelector("header #dropdown2-text").textContent = localStorage.getItem("defLang")
}

if (localStorage.getItem("defLang")?.includes("العربية")) {
    document.querySelector("body").style.direction = "rtl"
    if (document.querySelector(".search-icon")) {
        document.querySelector(".search-icon").classList.remove("right-3");
        document.querySelector(".search-icon").style.left = "10px";
    }
    if (document.querySelector(".toggle-pw")) {
        document.querySelectorAll(".toggle-pw").forEach(i => i.classList.remove("right-5"))
        document.querySelectorAll(".toggle-pw").forEach(i => i.style.left = "10px")
    }
}

dropDownOptions.forEach(i => {
    i.addEventListener("click", (e) => {
        localStorage.setItem("defLang", e.target.textContent)
        location.reload()
    })
})