import { Component, OnInit, ViewEncapsulation, ViewChild, Input } from '@angular/core';

import { LocalStorageService } from 'angular-2-local-storage';
import { AdminPanelService } from './admin-panel.service';
import { CompetitionDetailsService } from '../competitionDetails/competitionDetails.service';

declare global {
  interface JQuery {
    (any): JQuery;
    bracket(options: any): JQuery;
  }
}


@Component({
  selector: 'admin-panel',
  templateUrl: './admin-panel.component.html',
  styleUrls: ['./admin-panel.component.scss'],
  encapsulation: ViewEncapsulation.None
})

export class AdminPanleComponent {
  @Input() competitionData?: any;

  @ViewChild('download', {read: HTMLElement})
  download: HTMLElement;

  matchList;
  gironiList;

  constructor(private competitionDetailsService: CompetitionDetailsService,
    protected localStorageService: LocalStorageService,
    public adminPanelService: AdminPanelService,) { 
      this.competitionData = this.localStorageService.get('currentCompetition');
    }

  
  // ======================================================================== FLAGS ==================================================================================

  toggleFlags(flag){

    let request = {
      competition_id: this.competitionData.id,
      flag: flag
    };

    this.competitionDetailsService.toggleFlags(request).subscribe((response: any) => {

    });
  }

  // ======================================================================== FRONTESPIZI =========================================================================

  generaFrontespizi(){

    this.adminPanelService.generaFrontespizi({competitionId: this.competitionData.id}).subscribe(blob => {
      // Crea link per il download
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'Frontespizi.pdf';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
      
      // Nascondi loader...
  }, error => {
      console.error('Errore stampa frontespizi', error);
  });

  }

  // ======================================================================== SINTETICO CATEGORIA ==============================================================

  generaSinteticoCategoria(){

    this.adminPanelService.generaSinteticoCategoria({nomeGara: this.competitionData.nome_gara, competitionId: this.competitionData.id}).subscribe(blob => {
      // Crea un link temporaneo per scaricare il file
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'Sintetico_Categorie.pdf';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
      
      // Nascondi loader...
    }, error => {
        console.error('Errore stampa', error);
    }); 
    
  }

  //------------------------------------------------------------------ KUMITE -----------------------------------------------------------------------------------
  
 
  generaTabKumite(){

    let payload = {
      categoryId: null,
      competitionId: this.competitionData.id
    }
    this.adminPanelService.printBracketPdf(payload).subscribe((response:any) => {
      // 1. Crea un oggetto Blob con i dati ricevuti
            // response.body contiene i dati binari puri
            const blob = new Blob([response.body], { type: 'application/pdf' });

            // 2. Crea un URL temporaneo per il browser
            const url = window.URL.createObjectURL(blob);

            // 3. Crea un link <a> invisibile nel DOM
            const link = document.createElement('a');
            link.href = url;
            
            // 4. Imposta il nome del file (puoi prenderlo dagli header o metterne uno fisso)
            link.download = payload.categoryId ? 'Tabellone_Singolo.pdf' : 'Tutti_Tabelloni.pdf';
            
            // 5. Simula il click e rimuovi il link
            link.click();
            window.URL.revokeObjectURL(url); // Libera memoria
    });
  }

  // ========================================================================
  // STAMPA CARTELLINI
  // ========================================================================
  downloadAllPassesZip(){

    this.adminPanelService.downloadPassesZip({competitionId: this.competitionData.id}).subscribe({
      next: (blob: Blob) => {
        // 1. Creiamo un oggetto URL per il file ricevuto
        const url = window.URL.createObjectURL(blob);

        // 2. Creiamo un link <a> invisibile
        const link = document.createElement('a');
        link.href = url;

        // 3. Impostiamo il nome del file che verrà salvato sul PC
        link.download = 'Tutti_Pass_Atleti.zip';

        // 4. Simuliamo il click e poi rimuoviamo il link
        link.click();
        
        // 5. Pulizia memoria (importante per non intasare il browser)
        window.URL.revokeObjectURL(url);
      },
      error: (err) => {
        console.error('Errore durante il download dello ZIP', err);
        alert('Impossibile scaricare i pass. Verifica che ci siano iscritti.');
      }
    });
  }

}
