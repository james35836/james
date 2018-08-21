import { NgModule } from '@angular/core';
import { capitalizePipe } from './capitalize.pipe';
@NgModule(
{
	declarations: [capitalizePipe],
	exports: [capitalizePipe]
})

export class CustomPipeModule {}