import 'dotenv/config';
import 'module-alias/register';
import App from './app';
import StationController from '@/resources/station/station.controller';

const app = new App([new StationController()], Number(process.env.PORT));
app.listen();
