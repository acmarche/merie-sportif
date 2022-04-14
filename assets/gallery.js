import lightGallery from 'lightgallery';
// Plugins
import lgThumbnail from 'lightgallery/plugins/thumbnail'
import lgZoom from 'lightgallery/plugins/zoom'

/* j'arrive pas a lappeler depuis twig :-( */

//window.addEventListener("load", () => {

//});
export default function () {
    console.log('zeze');
    lightGallery(document.getElementById('lightgallery'), {
        plugins: [lgZoom, lgThumbnail],
        speed: 500,
        'selector': '.card-jf'
    });
};
