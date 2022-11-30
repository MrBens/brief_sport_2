let items = document.querySelectorAll('.item-container');
let subItems = document.querySelectorAll('.sub-item-container');
const panelNavBtn = document.querySelectorAll(".panel-nav-btn");


panelNavBtn.forEach(element => {
    if (window.location.href.includes(element.getAttribute('href'))) {
        element.classList.add('active')
    }
})


items.forEach(element => element.addEventListener('click', e => {
    if (element.contains(e.target) && !e.target.classList.contains('btn')) {
        items.forEach(item => {if(item.classList.contains('focus') && item != element) item.classList.remove('focus')})
        if(element.classList.contains('focus')){
            console.log('test');
            element.classList.remove('focus')
        }else{
            element.classList.add('focus')
        }
    }
}));

subItems.forEach(element => element.addEventListener('click', e => {
    if (element.contains(e.target) && !e.target.classList.contains('btn') && !e.target.classList.contains('item-container')) {
        items.forEach(item => { if (item.classList.contains('focus') && item != element) item.classList.remove('focus') })
        if (element.classList.contains('focus')) {
            element.classList.remove('focus')
        } else {
            element.classList.add('focus')
        }
    }
}));