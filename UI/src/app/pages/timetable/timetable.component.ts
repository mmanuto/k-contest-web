import { Component, Input, OnInit, ViewChild, ViewEncapsulation} from '@angular/core';
import { UntypedFormBuilder, FormGroup} from '@angular/forms';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { ToastrService } from 'ngx-toastr';
import { MenuService } from '../../theme/components/menu/menu.service';
import { environment } from '../../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { LocalStorageService } from 'angular-2-local-storage';
import { CompetitionDetailsService } from '../competitionDetails/competitionDetails.service';
 
interface CategoryPrediction {
  tatami: number;
  inizio: string;
  fine: string;
  categoria: string;
  tipo: string;
}

@Component({
  selector: 'timetable-panel',
  templateUrl: './timetable.component.html',
  styleUrls: ['./timetable.component.scss'],
  encapsulation: ViewEncapsulation.None,
  providers: [ MenuService ]
})
export class TimetableComponent implements OnInit {
  @Input() competitionData?: any;

  // Input utente
  tatamiPre: number = 9;
  tatamiAgo: number = 5;
  startTime: string = '09:00';
  categoriesList: any[] = []; // Questo verrebbe caricato dal tuo DB

  // Risultati raggruppati per Tatami
  scheduleByTatami: { [key: number]: CategoryPrediction[] } = {};
  tatamiIds: number[] = [];
  loading: boolean = false;

  
  constructor(private http: HttpClient,
              private competitionDetailsService: CompetitionDetailsService,
              public menuService:MenuService, 
              public modalService: NgbModal,
              public localStorageService: LocalStorageService) {

  }

  ngOnInit() {


    this.competitionDetailsService.getCategories({competition_id : this.competitionData.id}).subscribe((response: any) => {
      this.categoriesList = response.result.data;
    });

  }

  generatePrediction() {
    this.loading = true;
    const payload = {
      tatami_pre: this.tatamiPre,
      tatami_ago: this.tatamiAgo,
      start_time: this.startTime,
      categories: this.categoriesList
    };

    this.http.post<CategoryPrediction[]>(environment.BASEAPPURL + 'competitions/predict', payload)
      .subscribe({
        next: (res) => {
          this.groupByTatami(res);
          this.loading = false;
        },
        error: (err) => {
          console.error('Errore nel calcolo', err);
          this.loading = false;
        }
      });
  }

  private groupByTatami(data: CategoryPrediction[]) {
    this.scheduleByTatami = data.reduce((acc, curr) => {
      if (!acc[curr.tatami]) acc[curr.tatami] = [];
      acc[curr.tatami].push(curr);
      return acc;
    }, {} as any);
    
    this.tatamiIds = Object.keys(this.scheduleByTatami).map(Number).sort();
  }

  // Nel tuo timetable.component.ts aggiungi questo metodo helper
getTipoDaId(id: string): string {
  return id.substring(0, 3);
}

// Esempio d'uso per assegnare classi CSS dinamiche
getClassColor(id: string): string {
  const tipo = this.getTipoDaId(id);
  const mapping: {[key: string]: string} = {
    'KAG': 'color-kata-bambini',
    'KAS': 'color-kata-squadre',
    'KIA': 'color-kata-adulti',
    'KUA': 'color-kumite',
    'PER': 'color-percorso'
  };
  return mapping[tipo] || 'color-default';
}


}
