var CompanyContract = artifacts.requrie("CompanyContract");

module.exports = function(deployer){
    deployer.deploy(CompanyContract);
}