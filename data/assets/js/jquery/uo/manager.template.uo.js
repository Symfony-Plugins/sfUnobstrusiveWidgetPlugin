/**
 * Template manager.
 *
 * @licence     Dual licensed under the MIT (MIT-LICENSE.txt) and GPL (GPL-LICENSE.txt) licenses
 * @author      François Béliveau <francois.beliveau@my-labz.com>
 * @package     unobstrusive.manager
 * @version     0.10
 */
function uoTemplateManager(options)
{ 
  this.options  = options || {};

  if ('object' != typeof this.options)
  {
    this.options = {};
  }

  if (!this.options.var_prefix)
  {
    this.options.var_prefix = '%';
  }

  if (!this.options.var_suffix)
  {
    this.options.var_suffix = '%';
  }

  this.templates = {};
}

uoTemplateManager.prototype.add = function(key, value)
{
  if (!this.has(key))
  {
    this.templates[key] = value;
  }
  else
  {
    this._debug('Template "' + key + '" allready exists');
  }
}

uoTemplateManager.prototype.set = function(key, value)
{
  this.templates[key] = value;
}

uoTemplateManager.prototype.setAll = function(values)
{
  this.templates = values;
}

uoTemplateManager.prototype.get = function(key)
{
  if (this.has(key))
  {
    return this.templates[key];
  }
  
  return null;
}

uoTemplateManager.prototype.getAll = function()
{
  return this.templates;
}

uoTemplateManager.prototype.has = function(key)
{
  if (this.templates[key])
  {
    return true;
  }
  
  return false;
}

uoTemplateManager.prototype.remove = function(key)
{
  if (this.has(key))
  {
    delete this.templates[key];
  }
}

uoTemplateManager.prototype.reset = function()
{
  this.templates = {};
}

uoTemplateManager.prototype.count = function()
{
  return this.templates.length;
}

uoTemplateManager.prototype.render = function(key, data)
{
  var template = this.get(key);

  if (template && 'object' == typeof data)
  {
    for (var property in data)
    {
      if (data.hasOwnProperty(property))
      {
        template = template.replace(new RegExp(this.options.var_prefix + property + this.options.var_suffix, 'g'), data[property]);
      }
    }
  }
  else
  {
    this._debug('Invalid attributes');
  }

  return template;
}

uoTemplateManager.prototype._debug = function(message)
{
  if (window.console && window.console.log)
  {
    window.console.log(message);
  }
  else
  {
    alert(message);
  }
}
