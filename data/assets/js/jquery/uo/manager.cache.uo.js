/**
 * Cache manager.
 *
 * @licence     Dual licensed under the MIT (MIT-LICENSE.txt) and GPL (GPL-LICENSE.txt) licenses
 * @author      François Béliveau <francois.beliveau@my-labz.com>
 * @package     unobstrusive.manager
 * @version     0.10
 */
function uoCacheManager(options)
{ 
  this.options = options || {};
  this.cache   = {};
}

uoCacheManager.prototype.add = function(key, value)
{
}

uoCacheManager.prototype.has = function(key)
{
}

uoCacheManager.prototype.count = function()
{
}

uoCacheManager.prototype.set = function(cache)
{
}

uoCacheManager.prototype.get = function(key)
{
}

uoCacheManager.prototype.remove = function(key)
{
}

uoCacheManager.prototype.reset = function(key)
{
}

uoCacheManager.prototype.getAll = function(key)
{
  return this.cache;
}