import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import { WeddingformComponent } from './weddingform/weddingform.component';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { WeddingphotoComponent } from './weddingphoto/weddingphoto.component';
import { WeddingService } from './wedding.service';
import { HttpClientModule } from '@angular/common/http';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {MatButtonModule, MatCheckboxModule} from '@angular/material';
import {MatInputModule} from '@angular/material/input';
import {MatSelectModule} from '@angular/material/select';
import {MatFormFieldModule} from '@angular/material/form-field';

@NgModule({
  declarations: [
    AppComponent,
    WeddingformComponent,
    WeddingphotoComponent
  ],
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    HttpClientModule,
    FormsModule,
    ReactiveFormsModule,
    MatButtonModule,
    MatCheckboxModule,
    MatInputModule,
    MatSelectModule,
    MatFormFieldModule
  ],
  providers: [WeddingService],
  bootstrap: [AppComponent]
})
export class AppModule { }
