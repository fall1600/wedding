import { Component } from '@angular/core';
import { FormsModule, ReactiveFormsModule, Validators, FormBuilder } from '@angular/forms';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent  {
  ngOnInit() {
    this.registerEvents();
  };

  registerEvents() {
    document.addEventListener("keydown", (e) => {
      // Prevent F12
      if (e.keyCode == 123) { 
        e.preventDefault();
      }
      // Prevent Ctrl+Shift+I
      if (e.ctrlKey && e.shiftKey && e.keyCode == 73) { 
        e.preventDefault();
      }
    });
    document.addEventListener("contextmenu", (e) => {
      e.preventDefault();
    });
  }
}
