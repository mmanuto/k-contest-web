 
//PROD
export const IMAGES_WEBROOT = "https://" + document.location.hostname + "/apiV2/webroot/resources/";
export const BASEAPPURL = "https://" + document.location.hostname + "/apiV2/";

//DEV
//export const BASEAPPURL = "http://" + document.location.hostname + ":4200/backend/";
//export const IMAGES_WEBROOT = "http://" + document.location.hostname + ":4200/backend/webroot/resources/";


//COMPETITION STATUS

export const PENDING = 'PENDING'; // Gara in attesa
export const PUBLISHED = 'PUBLISHED'; // Gara pubblicata, iscrizioni chiuse
export const REG_OPEN = 'REG_OPEN'; // Iscrizioni aperte
export const REG_CLOSED_TIME = 'REG_CLOSED_TIME'; // Iscrizioni chiuse per limite di tempo
export const REG_CLOSED_FULL = 'REG_CLOSED_FULL'; // Iscrizioni chiuse per numero massimo
export const ARCHIVED = 'ARCHIVED'; // Gara chiusa, non più visibile

  