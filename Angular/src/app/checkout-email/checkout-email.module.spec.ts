import { CheckoutEmailModule } from './checkout-email.module';

describe('CheckoutEmailModule', () => {
  let checkoutEmailModule: CheckoutEmailModule;

  beforeEach(() => {
    checkoutEmailModule = new CheckoutEmailModule();
  });

  it('should create an instance', () => {
    expect(checkoutEmailModule).toBeTruthy();
  });
});
