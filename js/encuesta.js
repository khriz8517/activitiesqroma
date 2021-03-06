Vue.component("stars", {
    props: ["id"],
    data() {
        return {
            star_index: 0,
            fixed_stars: 0,
        };
    },
    methods: {
        calificarPregunta: function (numstars) {
            let frm = new FormData();
            frm.append("request_type", "encuestaRespByUser");
            frm.append("id", this.id);
            frm.append("puntaje", numstars);
            frm.append("sesskey", sesskey);
            axios.post("api/ajax_controller.php", frm).then((res) => {
                console.log(res);
            });
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
        preguntas: [],
    },
    created() {
        this.getPreguntasEncuesta();
    },
    methods: {
        getPreguntasEncuesta: function () {
            let frm = new FormData();
            frm.append("request_type", "getPreguntasEncuesta");
            axios.post("api/ajax_controller.php", frm).then((res) => {
                this.preguntas = res.data;
            });
        },
    },
});
