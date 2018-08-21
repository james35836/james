import { MemberRoutesModule } from './member-routes.module';

describe('MemberRoutesModule', () => {
  let memberRoutesModule: MemberRoutesModule;

  beforeEach(() => {
    memberRoutesModule = new MemberRoutesModule();
  });

  it('should create an instance', () => {
    expect(memberRoutesModule).toBeTruthy();
  });
});
