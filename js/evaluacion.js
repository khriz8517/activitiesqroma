var app = new Vue({
    el: "#app",
    delimiters: ["{(", ")}"],
    data: {
        preguntas: [],
        pActual: 0,
        resp_correctas: 0,
    },
    created() {
        this.getPreguntas();
    },
    computed: {
        progress: function () {
            var porcentaje = (this.pActual * 100) / this.preguntas.length;
            if (porcentaje > 100) {
                return 100;
            }
            return porcentaje;
        },
        result: function () {
            return (this.resp_correctas * 100) / this.preguntas.length;
        },
    },
    methods: {
        getPreguntas: function () {
            let frm = new FormData();
            frm.append("request_type", "getPreguntasOpcionesEvaluacion");
            axios.post("api/ajax_controller.php", frm).then((res) => {
                this.preguntas = res.data;
            });
        },
        opcionMarcada: function (pregunta, opcion) {
            if (opcion.is_valid === "1") {
                this.resp_correctas += 1;
            }
            console.log(
                "Pregunta ID: " + pregunta.id + " / Opcion ID: " + opcion.id
            );
        },
    },
});
