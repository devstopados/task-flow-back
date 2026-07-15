import SwaggerUIBundleImport from 'swagger-ui-dist/swagger-ui-bundle.js';
import SwaggerUIStandalonePresetImport from 'swagger-ui-dist/swagger-ui-standalone-preset.js';
import 'swagger-ui-dist/swagger-ui.css';
import spec from './swagger-spec.js';

const SwaggerUIBundle = SwaggerUIBundleImport.default || SwaggerUIBundleImport;
const SwaggerUIStandalonePreset = SwaggerUIStandalonePresetImport.default || SwaggerUIStandalonePresetImport;

SwaggerUIBundle({
    dom_id: '#swagger-ui',
    spec,
    presets: [
        SwaggerUIBundle.presets.apis,
        SwaggerUIStandalonePreset,
    ],
    layout: 'StandaloneLayout',
});
