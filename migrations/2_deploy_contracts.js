var CompanyContract = artifacts.require("CompanyContract");

module.exports = function(deployer){
    deployer.deploy(CompanyContract);
}