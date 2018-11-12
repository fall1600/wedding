import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import { WeddingformComponent } from './weddingform/weddingform.component';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { WeddingphotoComponent } from './weddingphoto/weddingphoto.component';
import { WeddingService } from './wedding.service';
import { HttpClientModule } from '@angular/common/http';

@NgModule({
  declarations: [
    AppComponent,
    WeddingformComponent,
    WeddingphotoComponent
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    FormsModule,
    ReactiveFormsModule,
  ],
  providers: [WeddingService],
  bootstrap: [AppComponent]
})
export class AppModule { }
