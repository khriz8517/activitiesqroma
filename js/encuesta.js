Vue.component("stars", {
    data() {
        return {
            star_index: 0,
            fixed_stars: 0,
        };
    },
    methods: {
        calificarPregunta: function (numstars) {
            console.log(numstars);
        },
    },
    template: `
        <div>
            <span 
                v-for="x in 5" 
                class="material-icons" 
                :class="{ active: star_index >= x, active_imp: fixed_stars >= x }" 
                @click="calificarPregunta(x), fixed_stars = x"
                @mouseenter="star_index = x" 
                @mouseleave="star_index = 0">star</span>
        </div>`,
});

var app = new Vue({
    el: "#app",
    delimiters: ["{(", ")}"],
    data: {
        preguntas: [
            { id: 1, pregunta: "¿Qué tanto te gustó el curso?" },
            { id: 2, pregunta: "¿Cómo calificas el contenido?" },
            { id: 3, pregunta: "¿Cómo calificas la presentación?" },
        ],
    },
});
