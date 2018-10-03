import { AdminRouteModule } from './admin-route.module';

describe('AdminRouteModule', () => {
  let adminRouteModule: AdminRouteModule;

  beforeEach(() => {
    adminRouteModule = new AdminRouteModule();
  });

  it('should create an instance', () => {
    expect(adminRouteModule).toBeTruthy();
  });
});
