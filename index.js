const hamburger = document.querySelector('.hamburger');
const menu = document.querySelector('.menu');

hamburger.addEventListener('click', () => {
    menu.classList.toggle('open');
    hamburger.classList.toggle('active');
});

document.querySelectorAll(".menu a").forEach(link=>{
    link.addEventListener("click",()=>{
        menu.classList.remove("open");
        hamburger.classList.remove("active");
    });
});