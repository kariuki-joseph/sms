/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");
const path = require("path");
require("bootstrap-slider");
import Vue from "vue";
import moment from "moment";
import VueRouter from "vue-router";
import { Form, HasError, AlertError } from "vform";
import VueProgressBar from "vue-progressbar";
import swal from "sweetalert2";
import Gate from "./Gate";
import TableData from "./TableData";
import "jszip/dist/jszip.min.js";
import "select2/dist/css/select2.min.css";
import "datatables/media/css/jquery.dataTables.min.css";
import "datatables/media/js/jquery.dataTables.min.js";
const XLSX = require("xlsx");
const FileSaver = require("file-saver");
import pdfMake from "pdfmake/build/pdfmake";
import pdfFonts from "pdfmake/build/vfs_fonts";
import axios from "axios";
import select2 from "select2";

pdfMake.vfs = pdfFonts.pdfMake.vfs;

window.Swal = swal;
window.Vue = require("vue");
window.Form = Form;
window.Fire = new Vue();
window.TableData = TableData;

Vue.prototype.$gate = new Gate(window.user);
Vue.use(VueRouter);
Vue.use(VueProgressBar, {
    color: "rgb(143,255,199)",
    failedColor: "red",
    height: "5px"
});
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);
Vue.component("pagination", require("laravel-vue-pagination"));

Vue.filter("upText", text => {
    return text == null || undefined || "" ?
        "" :
        text.charAt(0).toUpperCase() + text.slice(1);
});
Vue.filter("myDate", date => {
    return moment(date).format("MMMM Do YYYY");
});
Vue.filter("ifNotNull", (input, field) => {
    if (input == null) {
        return "Null";
    } else {
        switch (field) {
            case "mother_name":
                return input.mother_name ? input.mother_name : "Null";
                break;
            case "mother_contact":
                return input.mother_contact ? input.mother_contact : "Null";
                break;
            case "father_name":
                return input.father_name ? input.father_name : "Null";
                break;
            case "father_contact":
                return input.father_contact ? input.father_contact : "Null";
                break;
            case "guardian_name":
                return input.guardian_name ? input.guardian_name : "Null";
                break;
            case "guardian_contact":
                return input.guardian_contact ? input.guardian_contact : "Null";
                break;
        }
    }
});
Vue.filter("formatUpcoming", up => {
    return parseInt(up) == 1 ? "YES" : "NO";
});
Vue.filter("titleCase", text => {
    return text
        .split(" ")
        .map(name => name.charAt(0).toUpperCase() + name.substr(1, name.length))
        .join(" ");
});
Vue.filter("singularize", text => {
    if (text.substr(text.length - 2, text.length) == "es") {
        //mangoes
        return text.substr(0, text.length - 2);
    } else if (text.substr(text.length - 3, text.length) == "ies") {
        //categories
        return text.substr(0, text.length - 3) + "y";
    } else if (text.charAt(text.length - 1) == "s") {
        //workmates
        return text.substr(0, text.length - 1);
    } else {
        //workmate
        return text;
    }
});
Vue.component("not-found", require("./components/NotFound.vue").default);
Vue.component("table-header", require("./components/TableHeader.vue").default);

let routes = [
    { path: "/", component: require("./components/Students.vue").default },
    {
        path: "/classes",
        component: require("./components/Classes.vue").default
    },
    {
        path: "/profile",
        component: require("./components/Profile.vue").default
    },
    { path: "/users", component: require("./components/User.vue").default },
    {
        path: "/users/types",
        component: require("./components/UserTypes.vue").default
    },
    { path: "/roles", component: require("./components/Roles.vue").default },
    {
        path: "/settings",
        component: require("./components/Settings.vue").default
    },
    {
        path: "/students",
        component: require("./components/Students.vue").default
    },
    {
        path: "/teachers",
        component: require("./components/Teachers.vue").default
    },
    {
        path: "/:userType/:id/profile",
        component: require("./components/Profile.vue").default,
        name: "profile",
        props: true
    },
    {
        path: "/uploads",
        component: require("./components/Uploads.vue").default
    },
    {
        path: "/fees",
        component: require("./components/Fees.vue").default
    },
    {
        path: "/fees/payable",
        component: require("./components/PayableFees.vue").default
    },
    {
        path: "*",
        component: require("./components/NotFound.vue").default
    }
];
const toast = swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000
});

window.Toast = toast;

const router = new VueRouter({
    // mode: 'history',
    routes
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: "#app",
    router,
    data: {
        logs: {
            create: "Created a record",
            update: "Updated a record in",
            delete: "Deleted a record in"
        },
        settings: {}
    },
    methods: {
        createLog(activity) {
            let fd = new FormData();
            fd.append("_token", window.CSRF_TOKEN);
            fd.append("activity", activity);

            fetch("logs", {
                    method: "POST",
                    body: fd
                })
                .then(response => response.json())
                .then(response => {
                    console.log(response);
                })
                .catch(err => console.log(err));
        },
        generatePdf(options) {
            let data = options.data;
            let columns = Object.keys(data[0]);
            //convert to uppercase
            function populateTable(data, columns) {
                let body = [];
                body.push(columns);
                data.forEach(function(row) {
                    let rowData = [];
                    columns.forEach(col => rowData.push(row[col]));
                    body.push(rowData);
                });
                return body;
            }

            let docDefinition = {
                styles: {
                    header: {
                        fontSize: 18,
                        bold: true,
                        alignment: "center"
                    },
                    footer: {
                        fontSize: 16,
                        alignment: "center",
                        italics: true
                    },
                    mainHeadings: {
                        fontSize: 18,
                        bold: true,
                        alignment: "center"
                    },
                    normalText: {
                        fontSize: 16,
                        alignment: "left"
                    }
                },

                header: {
                    //header part
                },
                content: [{
                        stack: [{
                                image: this.settings.logo,
                                width: 100,
                                height: 100,
                                alignment: "center"
                            },
                            {
                                text: this.settings.sch_name.toUpperCase(),
                                style: ["mainHeadings"]
                            },
                            {
                                text: this.settings.po_address,
                                style: "mainHeadings"
                            },
                            {
                                text: `Telephone: ${
                                    this.settings.phone
                                }, Email: ${this.settings.email.toLowerCase()}`,
                                margin: [0, 0, 0, 15],
                                style: ["mainHeadings"]
                            }
                        ]
                    },
                    {
                        table: {
                            headerRows: 1,
                            widths: "*",
                            body: populateTable(data, columns)
                        }
                    }
                ],
                footer: [{
                    text: `${this.settings.sch_name
                            .split(" ")
                            .map(
                                name =>
                                    name.charAt(0).toUpperCase() +
                                    name.substr(1, name.length)
                            )
                            .join(" ")} - ${this.settings.sch_motto}`,
                    style: "footer"
                }]
            };
            //make the pdf document
            pdfMake
                .createPdf(docDefinition)
                .download(options.filename || "untitled.pdf");
            console.log("PDF download complete!!");
        },
        generateExcel(options) {
            const worksheet = XLSX.utils.json_to_sheet(options.data);
            const workbook = XLSX.utils.book_new();

            XLSX.utils.book_append_sheet(
                workbook,
                worksheet,
                "School Management System"
            );

            XLSX.writeFile(workbook, options.filename || "untitled.xlsx");
            console.log("Excel downladed successfully");
        },
        generateCsv(options) {
            const worksheet = XLSX.utils.json_to_sheet(options.data);
            const csv = XLSX.utils.sheet_to_csv(worksheet, { strip: true });
            FileSaver.saveAs(
                new Blob([csv], { type: "text/csv" }),
                options.filename || "untitled.csv"
            );
            console.log("CSV Download success");
        }
    },

    created() {
        //created lifecycle
    },
    mounted() {
        Fire.$on("generatePdf", options => {
            this.generatePdf(options);
        });
        Fire.$on("generateExcel", options => {
            this.generateExcel(options);
        });
        Fire.$on("generateCsv", options => {
            this.generateCsv(options);
        });

        //set up logo base64 image
        axios.get("settings").then(({ data }) => {
            this.settings = data.data[0];
        });
    },
    computed: {}
});