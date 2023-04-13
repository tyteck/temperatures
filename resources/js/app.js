import Alpine from 'alpinejs'
import Chart from 'chart.js/auto'

console.log('in app.js', Chart)
import '../css/app.css'

window.Alpine = Alpine

Alpine.start()
document.addEventListener('alpine:init', () => {
    console.log('alpine:init');
});
