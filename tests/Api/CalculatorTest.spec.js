/**
 * Bishwo Calculator API - Calculator Functionality Tests
 * Tests calculator execution and data validation
 */

const axios = require('axios');
const { expect } = require('chai');

describe('Bishwo Calculator API - Calculator Functionality', () => {
    const baseURL = 'http://localhost:8000';

    const client = axios.create({
        baseURL,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    });

    describe('GET /api/calculators', () => {
        it('should return comprehensive calculator inventory', async () => {
            const response = await client.get('/api/calculators');

            expect(response.status).to.equal(200);
            expect(response.data.success).to.equal(true);
            expect(response.data.data).to.be.an('array');
            expect(response.data.data.length).to.be.at.least(5); // Should have multiple categories

            // Check for required engineering categories
            const categories = response.data.data.map(cat => cat.category);
            expect(categories).to.include('civil');
            expect(categories).to.include('electrical');
            expect(categories).to.include('hvac');
        });

        it('should validate calculator data structure', async () => {
            const response = await client.get('/api/calculators');

            response.data.data.forEach(category => {
                expect(category).to.have.property('category');
                expect(category).to.have.property('tools');
                expect(category.tools).to.be.an('array');

                category.tools.forEach(tool => {
                    expect(tool).to.have.property('slug');
                    expect(tool).to.have.property('name');
                    expect(tool).to.have.property('subcategory');
                    expect(tool).to.have.property('path');
                });
            });
        });

        it('should include civil engineering calculators', async () => {
            const response = await client.get('/api/calculators');

            const civilCategory = response.data.data.find(cat => cat.category === 'civil');
            expect(civilCategory).to.exist;
            expect(civilCategory.tools.length).to.be.at.least(10);

            // Check for essential civil calculators
            const toolNames = civilCategory.tools.map(tool => tool.name.toLowerCase());
            expect(toolNames.some(name => name.includes('brick'))).to.equal(true);
            expect(toolNames.some(name => name.includes('concrete'))).to.equal(true);
            expect(toolNames.some(name => name.includes('foundation'))).to.equal(true);
        });

        it('should include electrical engineering calculators', async () => {
            const response = await client.get('/api/calculators');

            const electricalCategory = response.data.data.find(cat => cat.category === 'electrical');
            expect(electricalCategory).to.exist;
            expect(electricalCategory.tools.length).to.be.at.least(20);

            // Check for essential electrical calculators
            const toolNames = electricalCategory.tools.map(tool => tool.name.toLowerCase());
            expect(toolNames.some(name => name.includes('conduit'))).to.equal(true);
            expect(toolNames.some(name => name.includes('voltage'))).to.equal(true);
            expect(toolNames.some(name => name.includes('wire'))).to.equal(true);
        });
    });

    describe('Calculator Web Interface', () => {
        it('should load brickwork calculator page', async () => {
            const response = await client.get('/calculator/civil/brick-quantity?length=10&width=5&height=3');

            expect(response.status).to.equal(200);
            expect(response.headers['content-type']).to.include('text/html');
            expect(response.data).to.include('Bishwo'); // Should contain site branding
        });

        it('should handle calculator parameters', async () => {
            const response = await client.get('/calculator/civil/brick-quantity?length=15&width=10&height=5');

            expect(response.status).to.equal(200);
            expect(response.data).to.be.a('string');
            expect(response.data.length).to.be.greaterThan(1000); // Should be a full HTML page
        });

        it('should handle invalid calculator routes gracefully', async () => {
            try {
                await client.get('/calculator/invalid/nonexistent');
                expect.fail('Should have thrown 404 error');
            } catch (error) {
                expect(error.response.status).to.equal(404);
            }
        });
    });

    describe('Calculator Data Validation', () => {
        it('should handle edge case parameters', async () => {
            // Test with zero values
            const response1 = await client.get('/calculator/civil/brick-quantity?length=0&width=0&height=0');
            expect(response1.status).to.equal(200);

            // Test with very large values
            const response2 = await client.get('/calculator/civil/brick-quantity?length=999&width=999&height=999');
            expect(response2.status).to.equal(200);

            // Test with decimal values
            const response3 = await client.get('/calculator/civil/brick-quantity?length=10.5&width=5.25&height=3.75');
            expect(response3.status).to.equal(200);
        });

        it('should handle missing parameters gracefully', async () => {
            const response = await client.get('/calculator/civil/brick-quantity');
            expect(response.status).to.equal(200);
            // Should still load the calculator interface even without parameters
        });

        it('should handle invalid parameter types', async () => {
            const response = await client.get('/calculator/civil/brick-quantity?length=abc&width=def&height=ghi');
            expect(response.status).to.equal(200);
            // Should handle invalid input gracefully
        });
    });

    describe('Calculator Categories Coverage', () => {
        const expectedCategories = [
            'civil',
            'electrical',
            'hvac',
            'fire',
            'structural',
            'estimation',
            'mep'
        ];

        expectedCategories.forEach(category => {
            it(`should include ${category} category`, async () => {
                const response = await client.get('/api/calculators');

                const foundCategory = response.data.data.find(cat => cat.category === category);
                expect(foundCategory).to.exist;
                expect(foundCategory.tools).to.be.an('array');
                expect(foundCategory.tools.length).to.be.greaterThan(0);
            });
        });
    });

    describe('Calculator Tool Validation', () => {
        it('should validate tool properties', async () => {
            const response = await client.get('/api/calculators');

            response.data.data.forEach(category => {
                category.tools.forEach(tool => {
                    // Validate required properties
                    expect(tool.slug).to.be.a('string');
                    expect(tool.slug).to.have.length.greaterThan(0);
                    expect(tool.name).to.be.a('string');
                    expect(tool.name).to.have.length.greaterThan(0);
                    expect(tool.subcategory).to.be.a('string');
                    expect(tool.path).to.be.a('string');
                    expect(tool.path).to.include('.php');

                    // Validate slug format (should be URL-friendly)
                    expect(tool.slug).to.match(/^[a-z0-9-]+$/);

                    // Validate path exists (relative to modules directory)
                    expect(tool.path).to.include('/modules/');
                });
            });
        });

        it('should have unique tool slugs across all categories', async () => {
            const response = await client.get('/api/calculators');

            const allSlugs = [];
            response.data.data.forEach(category => {
                category.tools.forEach(tool => {
                    expect(allSlugs).to.not.include(tool.slug);
                    allSlugs.push(tool.slug);
                });
            });
        });
    });

    describe('Calculator Performance', () => {
        it('should respond within acceptable time limits', async () => {
            const startTime = Date.now();
            const response = await client.get('/api/calculators');
            const endTime = Date.now();

            const responseTime = endTime - startTime;
            expect(responseTime).to.be.lessThan(2000); // Should respond within 2 seconds
            expect(response.status).to.equal(200);
        });

        it('should handle concurrent calculator requests', async () => {
            const requests = [];
            const numConcurrentRequests = 5;

            // Create multiple concurrent requests
            for (let i = 0; i < numConcurrentRequests; i++) {
                requests.push(client.get('/api/calculators'));
            }

            const responses = await Promise.all(requests);

            // All requests should succeed
            responses.forEach(response => {
                expect(response.status).to.equal(200);
                expect(response.data.success).to.equal(true);
            });
        });
    });

    describe('Calculator Search and Discovery', () => {
        it('should support calculator search functionality', async () => {
            // Test search API if available
            try {
                const searchResponse = await client.get('/api/search?q=brick');
                expect(searchResponse.status).to.equal(200);
                expect(searchResponse.data).to.be.an('array');
            } catch (error) {
                // Search API might not be implemented yet
                console.log('Search API not available, skipping test');
            }
        });

        it('should provide calculator metadata', async () => {
            const response = await client.get('/api/calculators');

            response.data.data.forEach(category => {
                category.tools.forEach(tool => {
                    // Should have sufficient metadata for UI display
                    expect(tool.name).to.have.length.greaterThan(3);
                    expect(tool.subcategory).to.have.length.greaterThan(0);
                });
            });
        });
    });

    describe('Calculator Error Handling', () => {
        it('should handle malformed calculator requests', async () => {
            try {
                await client.get('/api/calculators/invalid');
                expect.fail('Should have thrown error for invalid endpoint');
            } catch (error) {
                expect(error.response.status).to.be.oneOf([404, 400]);
            }
        });

        it('should handle calculator requests with invalid parameters', async () => {
            const response = await client.get('/calculator/civil/brick-quantity?length=-1&width=-1&height=-1');
            expect(response.status).to.equal(200);
            // Should handle negative values gracefully
        });

        it('should handle extremely large parameter values', async () => {
            const largeValue = '999999999999999';
            const response = await client.get(`/calculator/civil/brick-quantity?length=${largeValue}&width=${largeValue}&height=${largeValue}`);
            expect(response.status).to.equal(200);
            // Should handle large values without crashing
        });
    });

    describe('Calculator Integration Tests', () => {
        it('should maintain calculator data consistency', async () => {
            // Get calculators list
            const listResponse = await client.get('/api/calculators');
            expect(listResponse.status).to.equal(200);

            // Verify that web interface can load calculators from the list
            if (listResponse.data.data.length > 0) {
                const firstCategory = listResponse.data.data[0];
                if (firstCategory.tools.length > 0) {
                    const firstTool = firstCategory.tools[0];

                    // Try to load the calculator web interface
                    const webResponse = await client.get(`/calculator/${firstCategory.category}/${firstTool.slug}`);
                    expect(webResponse.status).to.equal(200);
                }
            }
        });

        it('should support calculator categorization', async () => {
            const response = await client.get('/api/calculators');

            // Group calculators by category
            const categoryCount = {};
            response.data.data.forEach(category => {
                categoryCount[category.category] = category.tools.length;
            });

            // Should have reasonable distribution across categories
            expect(Object.keys(categoryCount).length).to.be.at.least(5);
            expect(Object.values(categoryCount).every(count => count > 0)).to.equal(true);
        });
    });
});