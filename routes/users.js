var express = require('express');
var handlebars = require('handlebars');
var router = express.Router();

/* GET users listing. */
router.get('/', function(req, res, next) {
  //res.send('respond with a resource');
  res.render('layout', {
    'title': 'Users',
    'reference' : {
      'text': 'See this link',
      'url': 'http://google.com'
    }
  })
});

handlebars.registerHelper('link', function(text, url) {
  text = handlebars.Utils.escapeExpression(text);
  url  = handlebars.Utils.escapeExpression(url);

  var result = '<a href="' + url + '">' + text + '</a>';

  return new handlebars.SafeString(result);
});

module.exports = router;
