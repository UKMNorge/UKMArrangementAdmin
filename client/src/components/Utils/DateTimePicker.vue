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
          label="Select pickup date & time"
          :prepend-icon="icon"
          readonly
          variant="outlined"
          v-bind="props"
        ></v-text-field>
      </template>
      
      <v-card>
        <!-- Date Picker -->
        <v-date-picker v-model="pickupDate" @change="updateDateText"></v-date-picker>
        
        <v-divider></v-divider>
        
        <!-- Time Picker (using v-select) -->
        <v-select
          v-if="pickupDate"
          v-model="pickupTime"
          :items="timeOptions"
          label="Select time"
          @change="updateDateText"
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
  import { ref } from "vue";
  
  export default {
    name: "DateTimePicker",
    props: {
      icon: {
        type: String as any, // Updated to accept valid IconValue (string or component)
        required: true,
      },
    },
    setup() {
      // State variables
      const menu = ref(false);
      const pickupDate = ref<string | null>(null);
      const pickupTime = ref<string | null>(null);
      const dateTimeText = ref("");
  
      // Time options (15-minute intervals)
      const timeOptions: string[] = [
        '00:00', '00:15', '00:30', '00:45',
        '01:00', '01:15', '01:30', '01:45',
        '02:00', '02:15', '02:30', '02:45',
        '03:00', '03:15', '03:30', '03:45',
        '04:00', '04:15', '04:30', '04:45',
        '05:00', '05:15', '05:30', '05:45',
        '06:00', '06:15', '06:30', '06:45',
        '07:00', '07:15', '07:30', '07:45',
        '08:00', '08:15', '08:30', '08:45',
        '09:00', '09:15', '09:30', '09:45',
        '10:00', '10:15', '10:30', '10:45',
        '11:00', '11:15', '11:30', '11:45',
        '12:00', '12:15', '12:30', '12:45',
        '13:00', '13:15', '13:30', '13:45',
        '14:00', '14:15', '14:30', '14:45',
        '15:00', '15:15', '15:30', '15:45',
        '16:00', '16:15', '16:30', '16:45',
        '17:00', '17:15', '17:30', '17:45',
        '18:00', '18:15', '18:30', '18:45',
        '19:00', '19:15', '19:30', '19:45',
        '20:00', '20:15', '20:30', '20:45',
        '21:00', '21:15', '21:30', '21:45',
        '22:00', '22:15', '22:30', '22:45',
        '23:00', '23:15', '23:30', '23:45',
      ];
  
      // Update date-time text
      const updateDateText = () => {
        if (pickupDate.value && pickupTime.value) {
          dateTimeText.value = `${pickupDate.value} at ${pickupTime.value}`;
        }
      };
  
      // Save handler
      const save = () => {
        menu.value = false;
        updateDateText();
      };
  
      return {
        menu,
        pickupDate,
        pickupTime,
        dateTimeText,
        timeOptions,
        updateDateText,
        save,
      };
    },
  };
  </script>
  