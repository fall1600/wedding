import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import { WeddingformComponent } from './weddingform/weddingform.component';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { WeddingphotoComponent } from './weddingphoto/weddingphoto.component';
import { WeddingService } from './wedding.service';

@NgModule({
  declarations: [
    AppComponent,
    WeddingformComponent,
    WeddingphotoComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
    ReactiveFormsModule,
  ],
  providers: [WeddingService],
  bootstrap: [AppComponent]
})
export class AppModule { }
