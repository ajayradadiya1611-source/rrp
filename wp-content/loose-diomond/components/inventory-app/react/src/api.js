import axios from "axios";

// Single shared axios instance for all inventory apps (App.jsx, App2.jsx,
// SimilarDiamonds.jsx, etc). Importing "axios" directly in multiple files
// let the bundler pull in the module more than once and merge them into
// the same minified scope, which corrupted internal helper references
// (e.g. "tp.merge is not a function"). Import this file instead of "axios".
export default axios;
