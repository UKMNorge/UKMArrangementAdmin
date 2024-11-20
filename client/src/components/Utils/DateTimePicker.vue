<template>
    <v-menu
      v-model="menu"
      :close-on-content-click="false"
      :nudge-right="40"
      transition="scale-transition"
      offset-y
      min-width="290px"
    >
      <template #activator="{ props }">
        <v-text-field
          class="v-text-field-arr-sys"
          v-model="dateTimeText"
          :label="title"
          :prepend-icon="icon"
          readonly
          variant="outlined"
          v-bind="props"
        ></v-text-field>
      </template>
      
      <v-card>
        <!-- Date Picker -->
        <v-date-picker v-model="pickupDate" @change="onDateChange"></v-date-picker>
        
        <v-divider></v-divider>
        
        <!-- Time Picker (using v-select) -->
        <v-select
          v-if="pickupDate"
          v-model="pickupTime"
          :items="timeOptions"
          label="Select time"
          @change="onTimeChange"
        ></v-select>
        
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="menu = false">Cancel</v-btn>
          <v-btn @click="save">OK</v-btn>
        </v-card-actions>
      </v-card>
    </v-menu>
  </template>
  
  <script lang="ts">
  import { ref, watch } from "vue";

import { useDate } from 'vuetify'
  

  export default {
    name: "DateTimePicker",
    props: {
      icon: {
        type: String,
        required: true,
      },
      title: {
        type: String,
        default: "Select date and time",
      },
      value: {
        type: Date,
        default: null,
      },
    },
    setup(props, { emit }) {

        
      // State variables
      const menu = ref(true);
      const pickupDate = ref<any | null>(null);
      const pickupTime = ref<any | null>(null);
      const dateTimeText = ref("");
  
      // Time options (15-minute intervals)
      const timeOptions: string[] = [
        '00:00', '00:15', '00:30', '00:45',
        // ... other time options
        '23:00', '23:15', '23:30', '23:45',
      ];
  
      // Initialize with incoming value prop
      if (props.value) {
        // const initialDate = new Date(props.value);
        // pickupDate.value = initialDate.toISOString().split('T')[0];
        // const hours = initialDate.getHours().toString().padStart(2, '0');
        // const minutes = initialDate.getMinutes().toString().padStart(2, '0');
        // pickupTime.value = `${hours}:${minutes}`;
        // dateTimeText.value = `${pickupDate.value} at ${pickupTime.value}`;
      }
  
      // Watch for changes from parent component
      watch(() => props.value, (newVal) => {
        alert('zzz');
        if (newVal) {
            alert('aaa');
            // const date = new Date(newVal);
            // console.log('setting');
            // console.log(date.toISOString().split('T')[0])
            // pickupDate.value = "2026-12-27";
        //   pickupDate.value = date.toISOString().split('T')[0];
        //   const hours = date.getHours().toString().padStart(2, '0');
        //   const minutes = date.getMinutes().toString().padStart(2, '0');
        //   pickupTime.value = `${hours}:${minutes}`;
        //   updateDateText();

            const date = '2023-11-30'

            console.log(new Date(date)) // Wed Nov 29 2023 18:00:00 GMT-0600
            const adapter = useDate()
            
            console.log(adapter.parseISO(date)) // Thu Nov 30 2023 00:00:00 GMT-0600
            console.log("start0005");
            pickupDate.value = adapter.parseISO(date);
            console.log("end0005");

        }
      });
  
      // Update date-time text
      const updateDateText = () => {
        if (pickupDate.value && pickupTime.value) {
          dateTimeText.value = `${pickupDate.value} at ${pickupTime.value}`;
        }
      };
  
      // Emit changes back to parent
      const emitDateChange = () => {
        if (pickupDate.value && pickupTime.value) {
          const [year, month, day] = pickupDate.value.split('-').map(Number);
          const [hours, minutes] = pickupTime.value.split(':').map(Number);
          const newDate = new Date(year, month - 1, day, hours, minutes);
          emit('update:value', newDate);
        }
      };
  
      const onDateChange = () => {
        updateDateText();
        emitDateChange();
      };
  
      const onTimeChange = () => {
        updateDateText();
        emitDateChange();
      };
  
      // Save handler
      const save = () => {
        console.log('Save clicked');
        console.log(pickupDate);
        console.log(pickupTime);
        menu.value = false;
        emitDateChange();
      };
  
      return {
        menu,
        pickupDate,
        pickupTime,
        dateTimeText,
        timeOptions,
        updateDateText,
        save,
        onDateChange,
        onTimeChange,
      };
    },
  };
  </script>
  