<template>
    <div class="wrap">

        <div class="form new-user" :id="form.id" v-for="form in forms" :key="form.name">
            <h1>{{form.name}}</h1>
            <div class="input-group" v-for="input in form.inputs" :key="input.name">
                {{input.name}} <input :type="input.type" v-model="form.inputsData[input.name]" :value="input.default" :placeholder="input.name" />
            </div>
            <div class="input-group">
                <button @click="formDo([form.submitAction, form])">Apply</button>
            </div>
        </div>

        <div v-for="(table, name, index) in ddd" :key="index">
            <h1>{{name}}</h1>
            <table class="table table-striped table-bordered">
                <thead v-if="table[0]">
                    <tr>
                        <th v-if="displayIndex(name)">#</th>
                        <th v-for="(colValue, colName) in filterColumns(table[0])" :key="colName">{{columnRename(colName)}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in table" :key="index" @click="goChangePass(row.id)">
                        <td v-if="displayIndex(name)">{{index + 1}}.</td>
                        <td v-for="(colValue, colName) in filterColumns(row)" :key="colName">
                            {{formatValue(colName, colValue)}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</template>

<style lang="scss" scoped>

    table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
        border-collapse: collapse;
        border-spacing: 2px;
    }

    h1 {
        text-transform: capitalize;
    }

    .wrap {
        padding: 20px;
    }

</style>

<script>

import {mapActions} from 'vuex';

import moment from 'moment';

export default {
    
    data() {
        return {
            ddd: {
                users:[]
            },

            forms: [
                {
                    name: "Nov uporabnik",
                    id: 'newUser',
                    inputs: [
                        {
                            name: 'name',
                            type: 'text'
                        },
                        {
                            name: 'email',
                            type: 'text',
                        },
                        {
                            name: 'grade_id',
                            type: 'number',
                            default: '2',
                        },
                        {
                            name: 'password',
                            type: 'text',
                        },
                    ],
                    submitAction: this.addNewUser,
                    inputsData: {id: ''},
                },
                {
                    name: "Spremeni geslo",
                    id: 'changePass',
                    inputs: [
                        {
                            name: 'id',
                            type: 'text'
                        },
                        {
                            name: 'password',
                            type: 'text'
                        },
                    ],
                    submitAction: this.updateUserPassword,
                    inputsData: {id: ''},
                },
            ],
        }
    },

    methods: {

        ...mapActions(['addNewUser', 'updateUserPassword', 'fetchAllUsers']),

        reloadData() {
            Promise.all([
                this.fetchAllUsers().then((d) => {this.ddd.users = d; }),
            ]).then(() => {
                console.log("Reloaded data.");
            });
        },

        goChangePass(id){

            debugger;
            this.forms[1].inputsData.id = id;
            document.getElementById('newUser').scrollIntoView();

        },

        displayIndex(tableName) {

            return ['players'].indexOf(tableName) >= 0;

        },

        formDo(funcs) {

            let func = funcs[0];
            let form = funcs[1];

            func(form.inputsData).then(()=> {

                alert("Uspešno posodobljeno");
                form.inputsData = {};

            }).catch((e)=> {
                console.log(e);
                alert("Napaka!\n\n " + e.message);
            });

        },

        columnRename(colName) {
            if(colName == "n_playbacks") return "#🔁📻";
            else if(colName == "n_additions") return "#✏➕";
            else if(colName == "n_deletions") return "#✏➖";
            else if(colName == "created_at") return "⌚";
            else if(colName == "rhythm_level") return "💪";

            else return colName;
        },

        filterColumns(cols) {
            let newObj = {};
            for(let c in cols) {
                if([
                    "id", "name", "email", "verified", "grade_id", "rhythm_level", "badges"
                ].indexOf(c) >= 0){
                    newObj[c] = cols[c];
                }
            }

            return newObj;
        },

        formatValue(colName, colValue) {
            if(colName == 'created_at') {
                moment.locale('sl');
                let k = moment(colValue).add(2, 'hours');
                return k.calendar();
            } else if(colName == 'type') {
                if(colValue == 'rhythm') return "R";
                else return "❌"
            } else if(colName== "success" || colName == 'finished') {
                if(colValue == "1") {return "✔"}
                return "❌";
            }

            return colValue;
        },

        fillDefaultValues() {

            for(let f of this.forms) {
                for(let i of f.inputs){
                    if(i.default){
                        f.inputsData[i.name] = i.default;
                    }
                }
            }

        }

    },

    mounted() {
        
        this.fillDefaultValues();

        let that = this;
        setInterval(()=> {
            that.reloadData();
        }, 3000);
        
        
    }

}
</script>


<style lang="scss" scoped>

$blackish : rgba(0,0,0,0.3);

.table {
  width: 100%;
  margin-bottom: 1rem;
  color: #212529;
  background: #FDBB2F;
}

.table th,
.table td {
  padding: 0.75rem;
  vertical-align: top;
  border-top: 1px solid $blackish;
}

.table thead th {
  vertical-align: bottom;
  border-bottom: 2px solid $blackish;
}

.table tbody + tbody {
  border-top: 2px solid $blackish;
}

.table-sm th,
.table-sm td {
  padding: 0.3rem;
}

.table-bordered {
  border: 1px solid $blackish;
}

.table-bordered th,
.table-bordered td {
  border: 1px solid $blackish;
}

.table-bordered thead th,
.table-bordered thead td {
  border-bottom-width: 2px;
}

.table-borderless th,
.table-borderless td,
.table-borderless thead th,
.table-borderless tbody + tbody {
  border: 0;
}

.table-striped tbody tr:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, 0.05);
}

.table-hover tbody tr:hover {
  color: #212529;
  background-color: rgba(0, 0, 0, 0.075);
}

.table-primary,
.table-primary > th,
.table-primary > td {
  background-color: #b8daff;
}

.table-primary th,
.table-primary td,
.table-primary thead th,
.table-primary tbody + tbody {
  border-color: #7abaff;
}

.table-hover .table-primary:hover {
  background-color: #9fcdff;
}

.table-hover .table-primary:hover > td,
.table-hover .table-primary:hover > th {
  background-color: #9fcdff;
}

.table-secondary,
.table-secondary > th,
.table-secondary > td {
  background-color: #d6d8db;
}

.table-secondary th,
.table-secondary td,
.table-secondary thead th,
.table-secondary tbody + tbody {
  border-color: #b3b7bb;
}

.table-hover .table-secondary:hover {
  background-color: #c8cbcf;
}

.table-hover .table-secondary:hover > td,
.table-hover .table-secondary:hover > th {
  background-color: #c8cbcf;
}

.table-success,
.table-success > th,
.table-success > td {
  background-color: #c3e6cb;
}

.table-success th,
.table-success td,
.table-success thead th,
.table-success tbody + tbody {
  border-color: #8fd19e;
}

.table-hover .table-success:hover {
  background-color: #b1dfbb;
}

.table-hover .table-success:hover > td,
.table-hover .table-success:hover > th {
  background-color: #b1dfbb;
}

.table-info,
.table-info > th,
.table-info > td {
  background-color: #bee5eb;
}

.table-info th,
.table-info td,
.table-info thead th,
.table-info tbody + tbody {
  border-color: #86cfda;
}

.table-hover .table-info:hover {
  background-color: #abdde5;
}

.table-hover .table-info:hover > td,
.table-hover .table-info:hover > th {
  background-color: #abdde5;
}

.table-warning,
.table-warning > th,
.table-warning > td {
  background-color: #ffeeba;
}

.table-warning th,
.table-warning td,
.table-warning thead th,
.table-warning tbody + tbody {
  border-color: #ffdf7e;
}

.table-hover .table-warning:hover {
  background-color: #ffe8a1;
}

.table-hover .table-warning:hover > td,
.table-hover .table-warning:hover > th {
  background-color: #ffe8a1;
}

.table-danger,
.table-danger > th,
.table-danger > td {
  background-color: #f5c6cb;
}

.table-danger th,
.table-danger td,
.table-danger thead th,
.table-danger tbody + tbody {
  border-color: #ed969e;
}

.table-hover .table-danger:hover {
  background-color: #f1b0b7;
}

.table-hover .table-danger:hover > td,
.table-hover .table-danger:hover > th {
  background-color: #f1b0b7;
}

.table-light,
.table-light > th,
.table-light > td {
  background-color: #fdfdfe;
}

.table-light th,
.table-light td,
.table-light thead th,
.table-light tbody + tbody {
  border-color: #fbfcfc;
}

.table-hover .table-light:hover {
  background-color: #ececf6;
}

.table-hover .table-light:hover > td,
.table-hover .table-light:hover > th {
  background-color: #ececf6;
}

.table-dark,
.table-dark > th,
.table-dark > td {
  background-color: #c6c8ca;
}

.table-dark th,
.table-dark td,
.table-dark thead th,
.table-dark tbody + tbody {
  border-color: #95999c;
}

.table-hover .table-dark:hover {
  background-color: #b9bbbe;
}

.table-hover .table-dark:hover > td,
.table-hover .table-dark:hover > th {
  background-color: #b9bbbe;
}

.table-active,
.table-active > th,
.table-active > td {
  background-color: rgba(0, 0, 0, 0.075);
}

.table-hover .table-active:hover {
  background-color: rgba(0, 0, 0, 0.075);
}

.table-hover .table-active:hover > td,
.table-hover .table-active:hover > th {
  background-color: rgba(0, 0, 0, 0.075);
}

.table .thead-dark th {
  color: #fff;
  background-color: #343a40;
  border-color: #454d55;
}

.table .thead-light th {
  color: #495057;
  background-color: #e9ecef;
  border-color: #dee2e6;
}

.table-dark {
  color: #fff;
  background-color: #343a40;
}

.table-dark th,
.table-dark td,
.table-dark thead th {
  border-color: #454d55;
}

.table-dark.table-bordered {
  border: 0;
}

.table-dark.table-striped tbody tr:nth-of-type(odd) {
  background-color: rgba(255, 255, 255, 0.05);
}

.table-dark.table-hover tbody tr:hover {
  color: #fff;
  background-color: rgba(255, 255, 255, 0.075);
}
</style>