import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-classifications',
  templateUrl: './classifications.component.html',
  styleUrls: ['./classifications.component.scss']
})
export class ClassificationsComponent implements OnInit {
  competition: any;
  categories: any[] = [];
  search = '';
  athleteSearch = '';
  categoryId = '';
  loading = true;
  error = '';
  constructor(private route: ActivatedRoute, private http: HttpClient) {}
  ngOnInit(){
    const id = this.route.snapshot.queryParamMap.get('competition_id');
    this.categoryId = this.route.snapshot.queryParamMap.get('categorycode_id') || '';
    if (!id) {
      this.error = 'Seleziona una gara dalla pagina iniziale.';
      this.loading = false;
      return;
    }
    this.http.post(environment.BASEAPPURL + 'competitions/getPublishedRankings', {competition_id: id})
      .subscribe({
        next: (response: any) => {
          this.loading = false;
          if (!response.result?.success) {
            this.error = response.result?.message || 'Classifiche non disponibili.';
            return;
          }
          this.competition = response.result.data.competition;
          this.categories = response.result.data.categories;
        },
        error: () => {
          this.loading = false;
          this.error = 'Impossibile caricare le classifiche. Riprova tra poco.';
        }
      });
  }
  get visibleCategories(){
    const term = this.search.trim().toLowerCase();
    return this.categories.filter(c =>
      (!this.categoryId || c.id === this.categoryId) &&
      [c.id, c.specialita, c.categoria, c.grado, c.sesso, c.cat_peso].join(' ').toLowerCase().includes(term) &&
      this.visibleRankings(c).length > 0);
  }
  visibleRankings(category: any){
    const term = this.athleteSearch.trim().toLowerCase();
    return category.rankings.filter(row =>
      !term || (row.cognome + ' ' + row.nome).toLowerCase().includes(term) ||
      (row.nome + ' ' + row.cognome).toLowerCase().includes(term));
  }
  position(category: any, rank: number){
    return category.id.includes('KU') && rank === 4 ? 3 : rank;
  }
}
