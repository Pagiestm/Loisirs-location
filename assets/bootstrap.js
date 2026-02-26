import { Application } from '@hotwired/stimulus';

import NavbarController from './controllers/navbar_controller';
import ReviewsController from './controllers/reviews_controller';
import FaqController from './controllers/faq_controller';

// CSRF protection — enregistre ses listeners automatiquement à l'import
import './controllers/csrf_protection_controller';

const app = Application.start();
app.register('navbar', NavbarController);
app.register('reviews', ReviewsController);
app.register('faq', FaqController);

export { app };
