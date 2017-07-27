var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/', function(req, res, next) {
  var context = {
    title: 'Express'
  }
  res.render('underconstruction', context);
});

module.exports = router;
