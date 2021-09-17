<template>
    <div class="container">
        <div v-if="successForm" class="succesForm">
            <div class="alert alert-success" role="alert">
                Ugurla Elave Olundu !
            </div>
        </div>
        <div v-if="!successForm" class="showForm">
            <div class="rezerv-form">
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <ul class="errors">
                            <li
                                v-for="error in errors"
                                :key="error.id"
                                class="bg-danger error-list"
                            >
                                {{ error }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <input
                                type="text"
                                class="form-control"
                                v-model="name"
                                placeholder="Ad Soyad"
                            />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <input
                                type="text"
                                class="form-control"
                                v-model="mail"
                                placeholder="E-Mail"
                            />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <input
                                type="text"
                                class="form-control"
                                v-model="phone"
                                v-mask="'+994(##)-###-##-##'"
                                placeholder="Phone"
                            />
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <input
                            @change="selectDate"
                            v-model="date"
                            type="date"
                            class="form-control"
                        />
                    </div>
                    <div class="col-lg-12 mt-3">
                        <select
                            class="form-control form-control-line"
                            v-model="notificationType"
                        >
                            <option disabled selected
                                >Bildiriş Növü Seçin</option
                            >
                            <option value="0">Mail</option>
                            <option value="1">Phone</option>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <ul class="select-time-ul mt-3">
                            <li
                                v-for="item in workingHours"
                                :key="item.id"
                                class="select-time"
                            >
                                <input
                                    type="radio"
                                    v-model="workingHour"
                                    v-bind:value="item.id"
                                    :disabled="
                                        item.isActive != true
                                            ? 'disabled'
                                            : null
                                    "
                                />
                                <span>{{ item.hours }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-12 mt-2">
                        <div class="form-group">
                            <textarea
                                class="form-control"
                                v-model="note"
                                placeholder="Not "
                            ></textarea>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <button
                            @click="store"
                            class="btn btn-success float-right"
                        >
                            Rezerv Et
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            successForm: false,
            errors: [],
            workingHour: 0,
            name: null,
            mail: null,
            phone: null,
            note: null,
            notificationType: null,
            date: new Date().toISOString().slice(0, 10),
            workingHours: []
        };
    },
    mounted() {
        axios.get("http://127.0.0.1:3000/api/working-hours").then(res => {
            this.workingHours = res.data;
        });
    },
    methods: {
        store: function() {
            if (
                this.name &&
                this.mail &&
                this.validEmail(this.mail) &&
                this.phone &&
                this.workingHour != 0
            ) {
                axios
                    .post("http://127.0.0.1:3000/api/appointment-add", {
                        fullname: this.name,
                        phone: this.phone,
                        email: this.mail,
                        date: this.date,
                        workinghour_id: this.workingHour,
                        notification_type: this.notificationType,
                        note: this.note
                    })
                    .then(res => {
                        if(res.data.operation){
                            this.successForm = true;
                        }
                        console.log(res);
                    });
            }
            this.errors = [];
            if (!this.name) {
                this.errors.push("Adı Boş Buraxmaq Olmaz !");
            }
            if (!this.mail) {
                this.errors.push("Maili Boş Buraxmaq Olmaz !");
            }
            if (!this.validEmail(this.mail)) {
                this.errors.push("Mail Düzgün Deyil !");
            }
            if (!this.phone) {
                this.errors.push("Nömrəni Boş Buraxmaq Olmaz !");
            }
            if (this.workingHour == 0) {
                this.errors.push("Vaxtı Boş Buraxmaq Olmaz !");
            }
        },
        selectDate: function() {
            axios
                .get(`http://127.0.0.1:3000/api/working-hours/${this.date}`)
                .then(res => {
                    this.workingHours = res.data;
                });
            console.log(this.date);
        },
        validEmail: function(email) {
            var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }
    }
};
</script>
