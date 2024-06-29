import { Component, EventEmitter, Output } from '@angular/core';
import { Stripe, loadStripe } from '@stripe/stripe-js';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-stripe-payment',
  standalone: true,
  imports: [],
  templateUrl: './stripe-payment.component.html',
  styleUrl: './stripe-payment.component.scss'
})
export class StripePaymentComponent {
  stripe: Stripe | null = null;
  isRequestPending: boolean = false;
  @Output() paymentInitiated = new EventEmitter();

  async ngOnInit(): Promise<void> {
    this.stripe = await loadStripe(environment.stripePublicKey);
  }

  async initiatePayment() {
    if (!this.stripe) {
      console.error('Stripe.js has not loaded yet.');
      return;
    }
    this.isRequestPending = true;

    this.paymentInitiated.emit();
  }
}
