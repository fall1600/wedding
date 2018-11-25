import { WeddingphotoComponent } from './app/weddingphoto/weddingphoto.component';
import { NgModule } from '@angular/core';
import { RouterModule, Routes, PreloadAllModules } from '@angular/router';
import { WeddingformComponent } from './app/weddingform/weddingform.component';

const appRoutes: Routes = [
  {
    path: 'tks',
    component: WeddingphotoComponent,
  },
  {
    path: '**',
    component: WeddingformComponent,
  }
];

@NgModule({
  imports: [
    RouterModule.forRoot(appRoutes,
      { preloadingStrategy: PreloadAllModules }
    ),
  ],
  exports: [
    RouterModule
  ]
})
export class AppRoutingModule { }
