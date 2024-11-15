<template>
    <div class="as-container container">
        <!-- Navn på arrangementet -->
        <div class="col-xs-12">
            <div class="as-card-1 as-padding-space-3 as-margin-bottom-space-2">
                <div class="as-margin-bottom-space-3">
                    <h4 class="">Detaljer</h4>
                </div>

                <v-text-field 
                    label="Navn på arrangementet" 
                    prepend-icon="mdi-tooltip-edit"
                    class="v-text-field-arr-sys"
                    variant="outlined">
                </v-text-field>
                    
                <v-text-field 
                    label="Hvor skal arrangementet være?" 
                    prepend-icon="mdi-map-marker"
                    class="v-text-field-arr-sys"
                    variant="outlined">
                </v-text-field>

                <div class="as-display-flex">
                    <div class="col-xs-6 nop-impt as-margin-right-space-2">
                        <DateTimePicker
                            icon="mdi-clock-start"
                        ></DateTimePicker>
                    </div>
                    <div class="col-xs-6 nop-impt as-margin-left-space-2">
                        <DateTimePicker
                            icon="mdi-clock-end"
                        ></DateTimePicker>
                    </div>
                </div>

                <div class="as-margin-top-space-3 as-margin-bottom-space-3">
                    <h4 class="">Status</h4>
                </div>

                <v-select
                    label="Velg type" 
                    variant="outlined" 
                    :prepend-icon="selectedStatus == 0 ? 'mdi-check-circle' : selectedStatus == 1 ? 'mdi-alert-circle' : 'mdi-close-circle'"
                    class="v-autocomplete-arr-sys" 
                    :items="statusTyper" 
                    v-model="selectedStatus"
                    item-text="title"
                    item-value="id" 
                    >
                </v-select>
            </div>
        </div>
    </div>
  </template>
  <script lang="ts">
  import MyObject from './../objects/myObject'
  import MainComponent from './MainComponent.vue';
  import DateTimePicker from './Utils/DateTimePicker.vue';
  import {StatusType} from './../objects/StatusType';

  export default {
      extends : MainComponent,
      props: {
          title: String,
      },
      components: {
          DateTimePicker : DateTimePicker
      },
      data() {
          return {
                statusTyper: [
                    new StatusType(0, 'Gjennomføres som planlagt'),
                    new StatusType(1, 'Gjennomføres men har viktig melding'),
                    new StatusType(2, 'Avlyses'),
                ] as StatusType[],
                selectedStatus: 0, // Initialize as null first
                myObject : new MyObject()
          }
      },
      mounted() {
        this.selectedStatus = 0; // Set this after `statusTyper` is defined
      },
      methods : {
          init(){
              console.log('ChildComponent.vue: init()');
              var myObjMethod = this.myObject.sayHello();
              console.log('From MyObject: ' + myObjMethod);
              
              // Kall super kompontent
              this.IaMsuperMethod();
          }
      }
  }
  
  </script>