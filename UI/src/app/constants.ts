 
//COMPETITION STATUS

export enum CompetitionState {
  Draft = 'DRAFT',  // Gara in attesa
  Published = 'PUBLISHED',   // Gara pubblicata, iscrizioni chiuse
  RegistrationOpen = 'REG_OPEN',  // Iscrizioni aperte
  RegistrationClosedTime = 'REG_CLOSED_TIME',  // Iscrizioni chiuse per limite di tempo
  RegistrationClosedFull = 'REG_CLOSED_FULL',  // Iscrizioni chiuse per numero massimo
  Archived = 'ARCHIVED',  // Gara chiusa, non più visibile
}


//COMPETITION TYPE

export enum EventType {
  Competition = 'CMP', // Gara
  Stage = 'STG', // Stage
}

//USER ROLE

export enum UserRole {
  Public = 'PUBLIC',       // Utente non loggato
  Authenticated = 'LOGGED_IN', // Utente loggato standard
  Admin = 'ADMIN',         // Amministratore
  SuperAdmin = 'SUPER_ADMIN', // Opzionale
}