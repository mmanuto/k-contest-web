import { NgModule } from '@angular/core';
import { Routes, RouterModule, PreloadAllModules  } from '@angular/router'; 
import { PagesComponent } from './pages/pages.component';
import { BlankComponent } from './pages/blank/blank.component';
import { SearchComponent } from './pages/search/search.component';
import { NotFoundComponent } from './pages/errors/not-found/not-found.component';

export const routes: Routes = [
  { path: '', redirectTo: 'register', pathMatch: 'full' },
  {
    path: '', 
    component: PagesComponent,
    children:[
      { path: '', loadChildren: () => import('./pages/dashboard/dashboard.module').then(m => m.DashboardModule), data: { breadcrumb: 'Dashboard' }  },          
      { path: 'membership', loadChildren: () => import('./pages/membership/membership.module').then(m => m.MembershipModule), data: { breadcrumb: 'Membership' } },
      { path: 'home', loadChildren: () => import('./pages/home/home.module').then(m => m.HomeModule), data: { breadcrumb: 'Home' } },
      { path: 'competitions', loadChildren: () => import('./pages/competitions/competitions.module').then(m => m.CompetitionsModule), data: { breadcrumb: 'Gare in corso' }  },
      { path: 'add-competition', loadChildren: () => import('./pages/add-competition/add-competition.module').then(m => m.AddCompetitionModule), data: { breadcrumb: 'Aggiungi Gara' }  },
      { path: 'competitionDetails/:id', loadChildren: () => import('./pages/competitionDetails/competitionDetails.module').then(m => m.CompetitionDetailsModule) },
      { path: 'riepilogo', loadChildren: () => import('./pages/riepilogo/riepilogo.module').then(m => m.RiepilogoModule), data: { breadcrumb: 'Riepilogo' } },
      { path: 'my-athletes', loadChildren: () => import('./pages/my-athletes/my-athletes.module').then(m => m.MyAthletesModule), data: { breadcrumb: 'I miei atleti' } },
      { path: 'personal', loadChildren: () => import('./pages/personal/personal.module').then(m => m.PersonalModule), data: { breadcrumb: 'Personal' } },
      { path: 'competitors-in-categories', loadChildren: () => import('./pages/competitorsInCategories/competitors-categories.module').then(m => m.CompetitorsInCategoriesModule), data: { breadcrumb: 'Atleti in categoria' } },
    ]
  },
  { path: 'login', loadChildren: () => import('./pages/login/login.module').then(m => m.LoginModule) },
  { path: 'register', loadChildren: () => import('./pages/register/register.module').then(m => m.RegisterModule) },
  { path: '**', component: NotFoundComponent }
]; 

@NgModule({
  imports: [
    RouterModule.forRoot(routes, {
      preloadingStrategy: PreloadAllModules, // <- comment this line for activate lazy load
      relativeLinkResolution: 'legacy',
      // useHash: true
    })
  ],
  exports: [
    RouterModule
  ]
})
export class AppRoutingModule { }