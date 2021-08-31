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
            var array_preguntas = [
                {
                    id: 1,
                    pregunta: "¿Qué mar navegan los astronautas?",
                    opciones: [
                        {
                            id: 1,
                            opcion: "Rios, lagos y lagunitas",
                            is_valid: false,
                        },
                        { id: 2, opcion: "Océano atlantico", is_valid: false },
                        { id: 3, opcion: "Océano pacífico", is_valid: false },
                        {
                            id: 4,
                            opcion: "Ninguna de las anteriores",
                            is_valid: true,
                        },
                    ],
                },
                {
                    id: 2,
                    pregunta:
                        "El agua potable tiene color amarillo verdoso clandestino.",
                    opciones: [
                        { id: 5, opcion: "Verdadero", is_valid: false },
                        { id: 6, opcion: "Falso", is_valid: true },
                    ],
                },
            ];
            this.preguntas = array_preguntas;
        },
        opcionMarcada: function (pregunta, opcion) {
            if (opcion.is_valid) {
                this.resp_correctas += 1;
            }
            console.log(
                "Pregunta ID: " + pregunta.id + " / Opcion ID: " + opcion.id
            );
        },
    },
});
