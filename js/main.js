Vue.component("modal", {
    props: ["material_item"],
    methods: {
        toggleModal: function () {
            this.$emit("toggle-modal");
        },
    },
    computed: {
        custom_style: function () {
            return this.material_item.format === ".pdf" ? "height: 35em" : "";
        },
    },
    template: `
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container" :style="custom_style">
                    <div @click="toggleModal" class="modal-close"><span class="material-icons">cancel</span></div>
                    <video v-if="material_item.format === '.mp4'" width="100%" controls>
                        <source :src="material_item.link_file" type="video/mp4">
                        Tu navegador no soporta video en HTML.
                    </video>
                    <object v-if="material_item.format === '.pdf'" data="http://www.africau.edu/images/default/sample.pdf" type="application/pdf" width="100%" height="100%">
                        <p>En caso no se visualice el PDF has click <a href="http://www.africau.edu/images/default/sample.pdf">aqui!</a></p>
                    </object>
                </div>
            </div>
        </div>`,
});

var app = new Vue({
    el: "#app",
    delimiters: ["{(", ")}"],
    data: {
        showModal: false,
        material: [
            {
                id: 1,
                material_title: "Nombre del primer archivo adjunto",
                updated: "Actualizado el 16 de junio de 2021",
                material_icon: "attachment",
                link_file: "",
                format: ".pdf",
            },
            {
                id: 2,
                material_title: "Nombre del video",
                updated: "Actualizado el 16 de junio de 2021",
                material_icon: "slow_motion_video",
                link_file: "",
                format: ".mp4",
            },
            {
                id: 3,
                material_title: "Nombre del segundo archivo",
                updated: "Actualizado el 16 de junio de 2021",
                material_icon: "attachment",
                link_file: "",
                format: ".pdf",
            },
        ],
        material_item: {},
    },
    methods: {
        toggleModal: function () {
            this.showModal = !this.showModal;
        },
        dynamicOptionCheckbox: function (id) {
            console.log(
                "aqui se ejecuta el metodo cuando se hace click en el checkbox de las opciones dinamicas"
            );
        },
        staticOptionCheckbox: function () {
            console.log(
                "aqui se ejecuta el metodo cuando se hace click en el checkbox de las opciones por defecto"
            );
        },
    },
});
