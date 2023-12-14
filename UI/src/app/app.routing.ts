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
      { path: 'pendingCompetitions', loadChildren: () => import('./pages/pendingCompetitions/pendingCompetitions.module').then(m => m.PendingCompetitionsModule), data: { breadcrumb: 'Gare in Sospeso' }  },
      { path: 'teams', loadChildren: () => import('./pages/teams/teams.module').then(m => m.TeamsModule), data: { breadcrumb: 'Squadre' } },
      { path: 'riepilogo', loadChildren: () => import('./pages/riepilogo/riepilogo.module').then(m => m.RiepilogoModule), data: { breadcrumb: 'Riepilogo' } },
      { path: 'settings', loadChildren: () => import('./pages/settings/settings.module').then(m => m.SettingsModule), data: { breadcrumb: 'Settings' } },
      { path: 'personal', loadChildren: () => import('./pages/personal/personal.module').then(m => m.PersonalModule), data: { breadcrumb: 'Personal' } },
      { path: 'competitors-in-categories', loadChildren: () => import('./pages/competitorsInCategories/competitors-categories.module').then(m => m.CompetitorsInCategoriesModule), data: { breadcrumb: 'Atleti in categoria' } },
      { path: 'ui', loadChildren: () => import('./pages/ui/ui.module').then(m => m.UiModule), data: { breadcrumb: 'UI' } },
      { path: 'form-elements', loadChildren: () => import('./pages/form-elements/form-elements.module').then(m => m.FormElementsModule), data: { breadcrumb: 'Form Elements' } },
      { path: 'tables', loadChildren: () => import('./pages/tables/tables.module').then(m => m.TablesModule), data: { breadcrumb: 'Tables' } },
      { path: 'tools', loadChildren: () => import('./pages/tools/tools.module').then(m => m.ToolsModule), data: { breadcrumb: 'Tools' } },
      { path: 'calendar', loadChildren: () => import('./pages/calendar/app-calendar.module').then(m => m.AppCalendarModule), data: { breadcrumb: 'Calendar' } },
      { path: 'mailbox', loadChildren: () => import('./pages/mailbox/mailbox.module').then(m => m.MailboxModule), data: { breadcrumb: 'Mailbox' } },
      { path: 'maps', loadChildren: () => import('./pages/maps/maps.module').then(m => m.MapsModule), data: { breadcrumb: 'Maps' } },
      { path: 'charts', loadChildren: () => import('./pages/charts/charts.module').then(m => m.ChartsModule), data: { breadcrumb: 'Charts' } },
      { path: 'profile', loadChildren: () => import('./pages/profile/profile.module').then(m => m.ProfileModule), data: { breadcrumb: 'Profile' }  },         
      { path: 'blank', component: BlankComponent, data: { breadcrumb: 'Blank page' } },
      { path: 'search', component: SearchComponent, data: { breadcrumb: 'Search' } }
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