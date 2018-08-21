import { CashierRoutesModule } from './cashier-routes.module';

describe('CashierRoutesModule', () => {
  let cashierRoutesModule: CashierRoutesModule;

  beforeEach(() => {
    cashierRoutesModule = new CashierRoutesModule();
  });

  it('should create an instance', () => {
    expect(cashierRoutesModule).toBeTruthy();
  });
});
