var Roulette = artifacts.require("Roulette");
var IterableMapping = artifacts.require("IterableMapping");
module.exports = function(deployer) {
  // deployment steps
  deployer.deploy(IterableMapping);
  deployer.link(IterableMapping, Roulette);
  deployer.deploy(Roulette);
};
